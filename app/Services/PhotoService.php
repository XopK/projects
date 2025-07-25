<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }

    public function upload($photo, $folder, $visibility = 'public_s3')
    {
        if ($photo && $photo->isValid()) {

            $filename = $this->hashPhoto($photo);

            $path = Storage::disk($visibility)->putFileAs($folder, $photo, $filename);

            if ($path) {
                return Storage::disk($visibility)->url($path);
            }
        }

        Log::info('Ошибка загрузки фото. Фото отсутсвует либо фото не прошло проверку!');

        return false;
    }

    public function deletePhoto(string $filePath, string $visibility = 'public_s3'): bool
    {
        $relativePath = str_replace(Storage::disk('public_s3')->url(''), '', $filePath);

        if (Storage::disk($visibility)->exists($relativePath)) {
            return Storage::disk($visibility)->delete($relativePath);
        }

        Log::warning("Файл для удаления не найден: {$relativePath}");

        return false;
    }

    public function hashPhoto($photo)
    {
        $extension = $photo->getClientOriginalExtension();
        $fileName = Str::random(40) . '.' . $extension;
        return $fileName;
    }

}
