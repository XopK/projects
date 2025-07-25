<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Group extends Model
{
    use AsSource;
    use Filterable;

    protected $table = 'groups';

    public const levels = [
        'beginner',
        'starter',
        'intermediate',
        'advanced',
    ];

    public const weeks = [
        'Пн',
        'Вт',
        'Ср',
        'Чт',
        'Пт',
        'Сб',
        'Вс',
    ];

    protected $fillable = [
        'title',
        'description',
        'time',
        'date',
        'price',
        'address_id',
        'preview',
        'video_group',
        'duration',
        'level',
        'class',
        'schedule',
        'active',
        'views',
        'age_verify',
        'status_block',
        'count_people',
        'user_id',
        'video_preview',
        'date_end',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Group $group) {
            $disk = Storage::disk('public_s3');

            // Функция для преобразования полного URL в относительный путь
            $getRelativePath = function ($url) {
                $base = 'https://s3.ru1.storage.beget.cloud/173cce6beae6-dramatic-lyle/';
                $relative = str_replace($base, '', $url);
                return preg_replace('/^public\//', '', $relative);
            };

            // Общая функция удаления файла
            $deleteFile = function ($url, $type) use ($disk, $getRelativePath, $group) {
                if (!$url) return;

                $relativePath = $getRelativePath($url);

                if ($disk->exists($relativePath)) {
                    $result = $disk->delete($relativePath);
                }
            };

            try {
                $deleteFile($group->preview, 'preview');
                $deleteFile($group->video_group, 'video');
                $deleteFile($group->video_preview, 'video');
            } catch (\Exception $e) {
                \Log::error("Ошибка удаления файла группы {$group->id}: " . $e->getMessage());
            }
        });
    }


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_groups');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOwner(): bool
    {
        return Auth::check() && $this->user_id == Auth::id();

    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(AddressList::class, 'address_id');
    }

    public function list_users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'list_users')
            ->withTimestamps()->withPivot('status_confirm');
    }

    public function countUser()
    {
        return $this->list_users()
            ->wherePivot('status_confirm', 1)
            ->count();
    }

    public function photoGroups(): HasMany
    {
        return $this->hasMany(PhotoGroup::class);
    }

    public function videoGroups(): HasMany
    {
        return $this->hasMany(VideoGroup::class);
    }

}
