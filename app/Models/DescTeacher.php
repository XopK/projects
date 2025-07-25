<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class DescTeacher extends Model
{
    protected $table = 'desc_teachers';

    protected $fillable = [
        'description',
        'experience',
        'photo_teacher',
        'bg_color'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (DescTeacher $desc_teacher) {
            $disk = Storage::disk('public_s3');

            $getRelativePath = function ($url) {
                $base = 'https://s3.ru1.storage.beget.cloud/173cce6beae6-dramatic-lyle/';
                $relative = str_replace($base, '', $url);
                return preg_replace('/^public\//', '', $relative);
            };

            $deleteFile = function ($url) use ($disk, $getRelativePath, $desc_teacher) {
                if (!$url) return;

                $relativePath = $getRelativePath($url);

                if ($disk->exists($relativePath)) {
                    $result = $disk->delete($relativePath);
                }
            };

            try {
                $deleteFile($desc_teacher->photo_teacher);
            } catch (\Exception $e) {
                \Log::error("Ошибка удаления файла {$desc_teacher->id}: " . $e->getMessage());
            }
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_teachers', 'teacher_id', 'category_id');
    }

}
