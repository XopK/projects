<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VideoGroup extends Model
{
    protected $table = 'video_groups';

    protected $fillable = [
        'video',
        'group_id',
        'preview',
    ];

    public static bool $skipBootDelete = false;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (VideoGroup $photoGroup) {
            if (self::$skipBootDelete) {
                return;
            }

            $disk = Storage::disk('public_s3');

            $getRelativePath = function ($url) {
                $base = 'https://s3.ru1.storage.beget.cloud/173cce6beae6-dramatic-lyle/';
                $relative = str_replace($base, '', $url);
                return preg_replace('/^public\//', '', $relative);
            };

            $deleteFile = function ($url) use ($disk, $getRelativePath, $photoGroup) {
                if (!$url) return;

                $relativePath = $getRelativePath($url);

                if ($disk->exists($relativePath)) {
                    $result = $disk->delete($relativePath);
                }
            };

            try {
                $deleteFile($photoGroup->video);
                $deleteFile($photoGroup->preview);
            } catch (\Exception $exception) {
                \Log::error($exception->getMessage());
            }

        });
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
