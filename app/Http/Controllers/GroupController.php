<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupCreateRequest;
use App\Models\AddressList;
use App\Models\Category;
use App\Models\Group;
use App\Models\PhotoGroup;
use App\Models\User;
use App\Models\VideoGroup;
use App\Services\Notifier;
use App\Services\PhotoService;
use App\Services\TelegramService;
use App\Services\VideoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    protected $photoService;
    protected $videoService;
    protected $telegramService;
    protected $notifier;

    public function __construct(PhotoService $photoService, VideoService $videoService, TelegramService $telegramService, Notifier $notifier)
    {
        $this->photoService = $photoService;
        $this->videoService = $videoService;
        $this->telegramService = $telegramService;
        $this->notifier = $notifier;
    }

    public function index()
    {
        $user = auth()->user();

        $categories = Category::orderBy('name', 'asc')->get();

        $teachers = User::whereHas('roles', function ($query) {
            $query->where('slug', 'teacher');
        })->orderBy('name', 'asc')->get();

        $addresses = AddressList::orderBy('studio_name', 'asc')->get();

        return view('pages.groups', ['categories' => $categories, 'teachers' => $teachers, 'addresses' => $addresses]);
    }

    public function group(Group $group)
    {
        $sessionKey = 'view_group_' . $group->id;

        $confirmedUsers = $group->list_users()
            ->wherePivot('status_confirm', 1)
            ->get();

        $group->status_for_user = 'none';

        $pivotUser = $group->list_users->find(auth()->id());

        if ($pivotUser) {
            $group->status_for_user = $pivotUser->pivot->status_confirm == 1 ? 'confirmed' : 'pending';
        }

        if (!session()->has($sessionKey)) {
            $group->increment('views');
            session()->put($sessionKey, true);
        }

        return view('pages.group', ['group' => $group, 'confirmedUsers' => $confirmedUsers]);
    }

    public function create(GroupCreateRequest $request)
    {
        try {
            $levels = $this->arrayParse($request->levels, Group::levels);
            $weeks = $this->arrayParse($request->selected_week, Group::weeks);

            $videoResult = $this->videoService->upload($request->file('video_group'), 'video_groups', 'public_s3');

            $group = Group::create([
                'title' => $request->title,
                'description' => $request->description,
                'count_people' => $request->count_people,
                'date' => $request->date,
                'date_end' => $request->date_end,
                'time' => $request->time,
                'price' => $request->price,
                'address_id' => $request->address,
                'preview' => $request->hasFile('preview') ?
                    $this->photoService->upload($request->file('preview'), 'photo_groups', 'public_s3') : null,
                'video_group' => $videoResult['video'] ?? null,
                'level' => $levels,
                'class' => $request->class,
                'duration' => $request->duration,
                'age_verify' => $request->has('isAdult') ? 1 : 0,
                'schedule' => $weeks,
                'active' => 1,
                'user_id' => Auth::user()->id,
                'video_preview' => $videoResult['preview'] ?? null,
            ]);

            $categoryIds = array_column($request->directions, 'id');
            $group->categories()->attach($categoryIds);

            return redirect()->route('profileMyGroups')->with('success', 'Группа создана!');

        } catch (\Exception $exception) {

            Log::error('Ошибка при создании группы:' . $exception->getMessage());

            return redirect()->route('profileMyGroups')->with('error', 'Ошибка создания группы! Попробуйте ещё раз.');

        }

    }

    private function arrayParse($array, $model)
    {
        $selectedOption = $array ?? [];
        $selectedArray = [];
        foreach ($model as $element) {
            $selectedArray[$element] = in_array($element, $selectedOption) ? 1 : 0;
        }

        return json_encode($selectedArray);
    }

    public function getGroupWithVideo(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $teacher = $request->get('teacher');
        $level = $request->get('levels');
        $class = $request->get('class');
        $address = $request->get('address');
        $date = $request->get('date');
        $time = $request->get('time');
        $sort = $request->get('sort');
        $field = $request->get('field');
        $isAdult = $request->get('adult');
        $allowedFields = ['created_at', 'title'];
        $allowedSorts = ['asc', 'desc'];

        $groupQuery = Group::select('id', 'title', 'description', 'video_group', 'age_verify', 'duration', 'video_preview', 'schedule', 'created_at', 'class', 'user_id', 'address_id', 'price', 'date', 'time', 'date_end')
            ->with(['user:id,name,nickname,photo_profile', 'address:id,studio_address,studio_name', 'categories:id,name']);

        if (auth()->check()) {
            if (!auth()->user()->isAdult()) {
                $groupQuery->where('age_verify', 0);
            }
        } elseif ($isAdult === 'false') {
            $groupQuery->where('age_verify', 0);
        }

        if (!empty($search)) {
            $groupQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if (!empty($category)) {
            $categories = explode(',', $category);

            $groupQuery->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('slug', $categories);
            });
        }

        if (!empty($teacher)) {
            $teachers = explode(',', $teacher);

            $groupQuery->whereIn('user_id', $teachers);
        }

        if (!empty($level)) {
            $levels = explode(',', $level);

            $groupQuery->where(function ($query) use ($levels) {
                foreach ($levels as $levelItem) {
                    $query->orWhere('level->' . $levelItem, 1);
                }
            });
        }

        if (!empty($class)) {
            $classes = explode(',', $class);

            $groupQuery->whereIn('class', $classes);
        }

        if (!empty($address)) {
            $addresses = explode(',', $address);

            $groupQuery->whereIn('address_id', $addresses);
        }

        if (!empty($date)) {
            [$weekday, $formattedDate] = array_map('trim', explode(',', $date));

            $groupQuery->whereDate('date', '<=', $formattedDate)
                ->where(function ($query) use ($weekday) {
                    $query->whereIn('class', ['class', 'private_lesson', 'guest_masterclass'])
                        ->orWhereJsonContains("schedule->$weekday", 1);
                });
        }

        if (!empty($time)) {
            $groupQuery->where('time', '=', $time);
        }

        if (in_array($field, $allowedFields) && in_array($sort, $allowedSorts)) {
            $groupQuery->orderBy($field, $sort);
        } else {
            $groupQuery->orderBy('created_at', 'desc');
        }

        $group = $groupQuery->paginate(3);

        $favorites = auth()->check()
            ? auth()->user()->favorites()->pluck('group_id')->toArray()
            : [];

        $group->getCollection()->transform(function ($item) use ($favorites) {
            $item->isFavorite = in_array($item->id, $favorites);
            $item->created_diff = Carbon::parse($item->created_at)->diffForHumans();

            $daysOrder = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

            if (is_string($item->schedule)) {
                $schedule = json_decode($item->schedule, true);
                if (!is_array($schedule)) {
                    $schedule = [];
                }
            } else {
                $schedule = $item->schedule ?? [];
            }

            $activeDays = collect($daysOrder)
                ->filter(fn($day) => !empty($schedule[$day]))
                ->values()
                ->all();

            $timeFormatted = '';
            if (!empty($item->time)) {
                try {
                    $timeFormatted = Carbon::parse($item->time)->format('H:i');
                } catch (\Exception $e) {
                    $timeFormatted = '';
                }
            }

            $item->readable_schedule = trim(implode(', ', $activeDays) . ' ' . $timeFormatted);

            return $item;
        });

        return response()->json($group, 200, ['success' => true]);
    }

    public function deleteGroup(Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'У вас нет прав на удаления группы!');
        }

        $group->delete();

        return redirect()->back()->with('success', 'Группа удалена!');
    }

    public function addFavorite(Group $group)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Пожалуйста, авторизуйтесь',
                'action' => 'unauthorized'
            ], 401);
        }

        $user = Auth::user();
        $owner = User::FindOrFail($group->user_id);

        $favorite = $user->favorites()->where('group_id', $group->id)->first();

        if ($favorite) {
            $favorite->delete();
            $this->notifier->announcement($user, $owner, $group, 'remove_favorite');

            return response()->json([
                'message' => 'Группа удалена из избранного.',
                'action' => 'delete'
            ]);
        }

        $user->favorites()->create([
            'group_id' => $group->id,
        ]);

        $this->notifier->announcement($user, $owner, $group, 'add_favorite');

        return response()->json([
            'message' => 'Группа добавлена в избранное.',
            'action' => 'add'
        ]);

    }

    public function registerUser(Group $group)
    {
        try {
            $clientUser = auth()->user();
            $owner = User::findOrFail($group->user_id);

            if (auth()->user()->id == $group->user_id) {
                return redirect()->back()->with('error', 'Вы не можете записаться в свой же набор!');
            }
            if ($group->countUser() >= $group->count_people) {
                return redirect()->back()->with('error', 'В наборе нет места!');
            }
            if ($group->list_users()->where('user_id', auth()->id())->exists()) {
                return redirect()->back()->with('error', 'Вы уже записались в этот набор!');
            }

            $group->list_users()->attach(auth()->user()->id);

            $this->notifier->announcement($clientUser, $owner, $group, 'register_user_on_group');

            return redirect()->back()->with('success', 'Вы успешно записались в набор!');

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return redirect()->back()->with('error', 'Ошибка записи в набор, попробуйте еще раз!');
        }

    }

    public function addPhoto(Request $request, Group $group)
    {
        try {
            if (auth()->user()->id != $group->user_id) {
                return redirect()->back()->with('error', 'Это не ваш набор!');
            }

            $request->validate([
                'photos' => 'required|array|min:1',
                'photos.*' => 'image|mimes:jpeg,jpg,png|max:5120'
            ]);

            foreach ($request->file('photos') as $photo) {
                $photoSave = PhotoGroup::create([
                    'group_id' => $group->id,
                    'photo' => $this->photoService->upload($photo, 'photo_groups', 'public_s3')
                ]);
            }

            return redirect()->back()->with('success', 'Фотографии были загружены!');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return redirect()->back()->with('error', 'Ошибка загрузки фотографии, попробуйте еще раз!');
        }

    }

    public function addVideo(Request $request, Group $group)
    {
        try {
            if (auth()->user()->id != $group->user_id) {
                return redirect()->back()->with('error', 'Это не ваш набор!');
            }

            $request->validate([
                'videos' => 'required|array|min:1',
                'videos.*' => 'mimes:mp4,webm,mov'
            ]);

            foreach ($request->file('videos') as $video) {
                $videoResult = $this->videoService->upload($video, 'video_groups', 'public_s3');

                $videoSave = VideoGroup::create([
                    'group_id' => $group->id,
                    'video' => $videoResult['video'] ?? null,
                    'preview' => $videoResult['preview'] ?? null,
                ]);
            }

            return redirect()->back()->with('success', 'Видео были загружены!');

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return redirect()->back()->with('error', 'Ошибка загрузки видео, попробуйте еще раз!');
        }
    }

    public function editGroup(Group $group)
    {
        if (auth()->user()->id != $group->user_id) {
            abort(404);
        }

        $categories = Category::orderBy('name', 'asc')->get();
        $addresses = AddressList::orderBy('created_at', 'desc')->get();

        $levelArray = json_decode($group->level, true);
        $selectedLevels = collect($levelArray ?? [])
            ->filter(fn($val) => $val)
            ->keys()
            ->toArray();

        $scheduleArray = json_decode($group->schedule ?? '{}', true);
        $selectedWeeks = collect($scheduleArray)
            ->filter()
            ->keys()
            ->toArray();

        return view('pages.edit_group', [
            'group' => $group,
            'categories' => $categories,
            'addresses' => $addresses,
            'selectedLevels' => $selectedLevels,
            'selectedWeeks' => $selectedWeeks,
        ]);
    }

    public function updateGroup(Request $request, Group $group)
    {

        $data = $request->all();
        $data['directions'] = json_decode($request->input('directions'), true);

        if (!is_array($data['directions'])) {
            return redirect()->back()->with('error', 'Неверный формат направлений.');
        }

        $validated = Validator::make($data, [
            'title' => 'required|string|max:255',
            'description' => 'required',
            'directions' => 'required|array|min:1|max:5',
            'levels' => 'required|array|max:4',
            'levels.*' => 'in:beginner,starter,intermediate,advanced',
            'count_people' => 'required|integer|min:1|max:99',
            'class' => 'required|in:regular_group,course,intensive,class,private_lesson,guest_masterclass',
            'date' => 'nullable|date_format:Y-m-d',
            'date_end' => 'nullable|date_format:Y-m-d',
            'time' => 'nullable|date_format:H:i',
            'selected_week' => 'required_if:is_schedule,true|array|min:1|max:7',
            'duration' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'address' => 'nullable',
        ])->validate();

        $levels = $this->arrayParse($validated['levels'], Group::levels);
        $weeks = $this->arrayParse($validated['selected_week'] ?? [], Group::weeks);

        $group->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'level' => $levels,
            'count_people' => $validated['count_people'],
            'class' => $validated['class'],
            'date' => $validated['date'],
            'date_end' => $validated['date_end'],
            'time' => $request->time,
            'schedule' => $weeks,
            'age_verify' => $request->has('isAdult') ? 1 : 0,
            'duration' => $validated['duration'],
            'price' => $validated['price'],
            'address_id' => $request->address,
        ]);

        $categoryIds = collect($data['directions'])->pluck('id')->toArray();
        $group->categories()->sync($categoryIds);

        return redirect()->route('groupEdit', $group->id)->with('success', 'Группа обновлена!');

    }

    public function deletePhoto(Request $request, $group)
    {
        $request->validate([
            'isPreview' => 'required|boolean',
            'photo' => 'required_if:isPreview,false'
        ]);
        $user = auth()->user();
        $isPreview = $request->input('isPreview');

        $group = Group::findOrFail($group);

        if ($group->user_id !== $user->id) {
            abort(403);
        }

        if (!$isPreview) {
            $photoId = intval($request->input('photo'));
            $photo = PhotoGroup::findOrFail($photoId);
            $photo->delete();
        } else {
            $firstPhoto = PhotoGroup::where('group_id', $group->id)->first();

            if ($group->preview) {
                $this->deleteFromS3($group->preview);
            }

            if ($firstPhoto) {
                $group->preview = $firstPhoto->photo;
                PhotoGroup::$skipBootDelete = true;
                $firstPhoto->delete();
                PhotoGroup::$skipBootDelete = false;
            } else {
                $group->preview = null;

            }

            $group->save();
        }

        return redirect()->back()->with('success', 'Фотография удалена!');

    }

    public function updatePreview(Request $request, $group)
    {
        $request->validate([
            'photo' => 'required',
        ]);

        $user = auth()->user();
        $photo = PhotoGroup::findOrFail($request->photo);
        $group = Group::findOrFail($group);

        if ($group->user_id !== $user->id || $photo->group_id !== $group->id) {
            abort(403);
        }

        if ($group->preview) {
            PhotoGroup::create([
                'group_id' => $group->id,
                'photo' => $group->preview,
            ]);
        }

        $group->preview = $photo->photo;

        PhotoGroup::$skipBootDelete = true;
        $photo->delete();
        PhotoGroup::$skipBootDelete = false;

        $group->save();

        return redirect()->back()->with('success', 'Превью обновлено!');
    }

    public function deleteVideo(Request $request, $group)
    {
        $request->validate([
            'isPreview' => 'required|boolean',
            'video' => 'required_if:isPreview,false'
        ]);

        $user = auth()->user();
        $isPreview = $request->input('isPreview');

        $group = Group::findOrFail($group);

        if ($group->user_id !== $user->id) {
            abort(403);
        }

        if (!$isPreview) {
            $videoId = intval($request->input('video'));
            $video = VideoGroup::findOrFail($videoId);
            $video->delete();
        } else {
            $firstVideo = VideoGroup::where('group_id', $group->id)->first();

            if (!$firstVideo) {
                return redirect()->back()->with('error', 'Добавьте еще одно видео при удалении превью набора.');
            }

            if ($group->video_group) {
                $this->deleteFromS3($group->video_group);
                $this->deleteFromS3($group->video_preview);
            }

            $group->video_group = $firstVideo ? $firstVideo->video : null;
            $group->video_preview = $firstVideo ? $firstVideo->preview : null;
            VideoGroup::$skipBootDelete = true;
            $firstVideo->delete();
            VideoGroup::$skipBootDelete = false;

            $group->save();
        }

        return redirect()->back()->with('success', 'Видео удалено!');
    }

    public function updatePreviewVideo(Request $request, $group)
    {
        $request->validate([
            'video' => 'required',
        ]);

        $user = auth()->user();
        $video = VideoGroup::findOrFail($request->video);
        $group = Group::findOrFail($group);

        if ($group->user_id !== $user->id || $video->group_id !== $group->id) {
            abort(403);
        }

        if ($group->video_group) {
            VideoGroup::create([
                'group_id' => $group->id,
                'video' => $group->video_group,
                'preview' => $group->video_preview,
            ]);
        }

        $group->video_group = $video->video;
        $group->video_preview = $video->preview;

        VideoGroup::$skipBootDelete = true;
        $video->delete();
        VideoGroup::$skipBootDelete = false;

        $group->save();

        return redirect()->back()->with('success', 'Превью обновлено!');
    }

    private function deleteFromS3(string $url): void
    {
        $disk = Storage::disk('public_s3');

        $getRelativePath = function ($url) {
            $base = 'https://s3.ru1.storage.beget.cloud/173cce6beae6-dramatic-lyle/';
            $relative = str_replace($base, '', $url);
            return preg_replace('/^public\//', '', $relative);
        };

        try {
            $relativePath = $getRelativePath($url);

            if ($disk->exists($relativePath)) {
                $disk->delete($relativePath);
            }
        } catch (\Exception $e) {
            \Log::error("Ошибка при удалении preview: " . $e->getMessage());
        }
    }

    public function deleteUser(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        $user = auth()->user();
        $groupId = $request->get('group_id');

        if (!$user->list_groups()->where('group_id', $groupId)->exists()) {
            return redirect()->back()->with('error', 'Вы не записаны в этот набор!');
        }

        $group = Group::findOrFail($groupId);
        $owner = User::findOrFail($group->user_id);

        $user->list_groups()->detach($groupId);

        $this->notifier->announcement($user, $owner, $group, 'cancel_user_on_group');

        return redirect()->back()->with('success', 'Запись отменена!');

    }
}
