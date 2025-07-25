<?php

namespace App\Http\Controllers;

use App\Models\AddressList;
use App\Models\Category;
use App\Models\DescTeacher;
use App\Models\Group;
use App\Models\User;
use App\Notifications\UserCancelRegistration;
use App\Services\PhotoService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherController extends Controller
{

    protected $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    public function getTeacher(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('categories');
        $level = $request->get('levels');
        $class = $request->get('class');
        $address = $request->get('address');
        $sort = $request->get('sort');
        $field = $request->get('field');

        $allowedFields = ['count', 'name'];
        $allowedSorts = ['asc', 'desc'];

        $teachersQuery = User::whereHas('roles', function ($query) {
            $query->where('slug', 'teacher');
        })->select('id', 'name', 'nickname', 'photo_profile');

        if (!empty($search)) {
            $teachersQuery->where('name', 'like', '%' . $search . '%');
        }

        if (!empty($category)) {
            $categories = explode(',', $category);

            $teachersQuery->whereHas('groups', function ($groupQuery) use ($categories) {
                $groupQuery->whereHas('categories', function ($catQuery) use ($categories) {
                    $catQuery->whereIn('slug', $categories);
                });
            });
        }

        if (!empty($level)) {
            $levels = explode(',', $level);

            $teachersQuery->whereHas('groups', function ($groupQuery) use ($levels) {
                $groupQuery->where(function ($query) use ($levels) {
                    foreach ($levels as $levelItem) {
                        $query->orWhere('level->' . $levelItem, 1);
                    }
                });
            });
        }

        if (!empty($class)) {
            $classes = explode(',', $class);

            $teachersQuery->whereHas('groups', function ($groupQuery) use ($classes) {
                $groupQuery->whereIn('class', $classes);
            });
        }

        if (!empty($address)) {
            $addresses = explode(',', $address);

            $teachersQuery->whereHas('groups', function ($groupQuery) use ($addresses) {
                $groupQuery->whereIn('address_id', $addresses);
            });
        }

        if (in_array($field, $allowedFields) && in_array($sort, $allowedSorts)) {
            if ($field === 'name') {
                $teachersQuery->orderBy('name', $sort);
            } elseif ($field === 'count') {
                $teachersQuery->withCount('groups')->orderBy('groups_count', $sort);
            }
        }

        $teachers = $teachersQuery->orderBy('name', 'asc')->paginate(9);

        return response()->json($teachers, 200, ['success' => true]);
    }

    public function teachers()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $addresses = AddressList::orderBy('studio_name', 'asc')->get();

        return view('pages.teachers', ['categories' => $categories, 'addresses' => $addresses]);
    }

    public function teacher($teacher)
    {
        $categories = Category::orderBy('name', 'asc')->get();

        $teacherData = User::with(['groups', 'descTeacher', 'roles'])
            ->select('id', 'name', 'nickname', 'photo_profile', 'created_at')
            ->findOrFail($teacher);

        $months = $teacherData->descTeacher->experience ?? 0;

        $years = intdiv($months, 12);
        $remainingMonths = $months % 12;

        $parts = [];

        if ($years > 0) {
            $parts[] = "{$years} " . $this->plural($years, 'год', 'года', 'лет');
        }

        if ($remainingMonths > 0) {
            $parts[] = "{$remainingMonths} " . $this->plural($remainingMonths, 'месяц', 'месяца', 'месяцев');
        }

        $teacherData->formated_experience = $parts ? implode(' и ', $parts) : 'меньше месяца';

        if (!$teacherData->roles->contains('slug', 'teacher')) {
            abort(404);
        }

        return view('pages.teacher', ['teacher' => $teacherData, 'categories' => $categories]);
    }

    private function plural($number, $one, $few, $many)
    {
        $number = abs($number) % 100;
        $n1 = $number % 10;

        if ($number > 10 && $number < 20) return $many;
        if ($n1 > 1 && $n1 < 5) return $few;
        if ($n1 == 1) return $one;

        return $many;
    }


    public function updateTeacher(Request $request, $teacher)
    {
        $request->validate([
            'description' => 'nullable|max:255',
            'directions' => 'nullable|string',
            'experience' => 'nullable|integer|min:0',
            'bg_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'photo_teacher' => 'nullable|image|mimes:jpeg,png,jpg|max:51200',
        ]);

        $desc = DescTeacher::where('teacher_id', $teacher)->firstOrFail();

        $desc->update($request->only('description', 'bg_color', 'experience'));

        if ($request->hasFile('photo_teacher')) {
            $uploadedPhoto = $this->photoService->upload($request->file('photo_teacher'), 'photo_teachers', 'public_s3');

            $desc->update(['photo_teacher' => $uploadedPhoto]);
        }

        if ($request->directions) {
            $directionsArray = json_decode($request->directions, true);
            if (is_array($directionsArray)) {
                $categoryIds = array_column($directionsArray, 'id');
                $validCategoryIds = Category::whereIn('id', $categoryIds)->pluck('id')->toArray();
                $desc->categories()->sync($validCategoryIds);
            }
        }

        return redirect()->back()->with('success', 'Информация обновлена.');
    }

    public function deleteTeacherPhoto(Request $request, $teacher)
    {
        if (!$teacher) {
            abort(404);
        }

        if (auth()->user()->id !== (int)$teacher) {
            abort(403);
        }

        $desc = DescTeacher::where('teacher_id', $teacher)->firstOrFail();

        if ($this->photoService->deletePhoto($desc->photo_teacher, 'public_s3')) {
            $desc->photo_teacher = null;
            $desc->save();

            return redirect()->back()->with('success', 'Фото профиля удалено.');
        }

        return redirect()->back()->with('error', 'Ошибка удаления фото, попробуйте ещё раз.');

    }

    public function teacherVideo(Request $request, $teacher)
    {
        $search = $request->get('search');
        $category = $request->get('categories');
        $level = $request->get('levels');
        $class = $request->get('class');
        $sort = $request->get('sort');
        $field = $request->get('field');

        $allowedFields = ['created_at', 'title'];
        $allowedSorts = ['asc', 'desc'];

        $groupQuery = Group::select('id', 'title', 'description', 'video_group', 'duration', 'video_preview', 'created_at', 'class', 'user_id', 'address_id', 'price', 'date', 'time', 'date_end', 'schedule')->with(['user:id,name,nickname,photo_profile', 'address:id,studio_address,studio_name', 'categories:id,name'])->where('user_id', $teacher);

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

    public function userInfo(User $user)
    {
        $query = Group::with('list_users')
            ->where('user_id', auth()->id())
            ->whereHas('list_users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('categories', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        if (in_array($sortField, ['created_at', 'title']) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        $groups = $query->paginate(6)->withQueryString();

        foreach ($groups as $group) {
            $group->statusConf = $group->list_users()->wherePivot('status_confirm', 1)->exists();
        }

        return view('pages.user', ['user' => $user, 'groups' => $groups]);
    }

    public function deleteUserTeacher(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->get('user_id');
        $groupId = $request->get('group_id');
        $user = User::findOrFail($userId);
        $owner = auth()->user();
        $group = Group::findOrFail($groupId);

        if ($owner->id !== $group->user_id) {
            return redirect()->back()->with('error', 'Это не ваш набор!');
        }

        $owner->notify(new UserCancelRegistration($user, $group));

        $user->list_groups()->detach($groupId);

        return redirect()->back()->with('success', 'Запись отменена!');
    }
}
