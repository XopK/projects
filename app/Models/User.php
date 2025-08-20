<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nickname',
        'phone',
        'email',
        'birthday',
        'photo_profile',
        'password',
        'notify_tg',
        'notify_site',
        'notify_email',
        'username_telegram',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
        'chat_id_telegram',
        'token_telegram',
        'vk_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'email' => Like::class,
        'phone' => Like::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function (User $user) {
            $disk = Storage::disk('public_s3');

            $getRelativePath = function ($url) {
                $base = 'https://s3.ru1.storage.beget.cloud/173cce6beae6-dramatic-lyle/';
                $relative = str_replace($base, '', $url);
                return preg_replace('/^public\//', '', $relative);
            };

            $deleteFile = function ($url) use ($disk, $getRelativePath, $user) {
                if (!$url) return;

                $relativePath = $getRelativePath($url);

                if ($disk->exists($relativePath)) {
                    $disk->delete($relativePath);
                }
            };

            try {
                $oldAvatar = $user->getOriginal('photo_profile');

                if ($user->isDirty('photo_profile') && $oldAvatar) {
                    $deleteFile($oldAvatar);
                }
            } catch (\Exception $exception) {
                \Log::error("Ошибка удаления файла у пользователя ID {$user->id}: " . $exception->getMessage());
            }
        });
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_users');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function hasAccessChat(int $userId, int $chatId): bool
    {
        return $this->chats()->whereHas('users', function ($query) use ($userId, $chatId) {
            $query->where('chat_id', $chatId)
                ->where('user_id', $userId);
        })->exists();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;

        if (isset($this->attributes['photo_profile']) &&
            str_contains($this->attributes['photo_profile'], 'ui-avatars.com')) {

            $this->attributes['photo_profile'] = 'https://ui-avatars.com/api/?background=09090b&color=fff&name=' . urlencode($value) . '&size=128&length=1';
        }
    }

    public function list_groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'list_users')
            ->withPivot('status_confirm');
    }

    public function descTeacher(): HasOne
    {
        return $this->hasOne(DescTeacher::class, 'teacher_id');
    }

    public function isAdult()
    {
        return Carbon::parse($this->birthday)->age >= 18;
    }
}
