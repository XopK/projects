<?php

namespace App\Services;

use FFMpeg\Format\Video\X264;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoService
{
    /**
     * Create a new class instance.
     */
    protected $ffmpeg;

    public function __construct()
    {
        $this->ffmpeg = app('ffmpeg');
    }

    public function upload($video, $folder, $visibility = 'public_s3')
    {
        if ($video && $video->isValid()) {
            $fileName = $this->hashVideo($video);
            $tempDir = storage_path('app/temp');
            $tempPath = $tempDir . '/' . $fileName;

            try {

                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0777, true);
                }

                $video->move($tempDir, $fileName);

                $path = Storage::disk($visibility)->putFileAs($folder, new File($tempPath), $fileName);

                $videoUrl = Storage::disk($visibility)->url($path);
                $previewUrl = $this->makePreview($tempPath, $folder, $visibility);

                return [
                    'video' => $videoUrl,
                    'preview' => $previewUrl,
                ];

            } catch (\Exception $exception) {

                Log::error('Ошибка обработки видео: ' . $exception->getMessage());
                Log::error($exception->getTraceAsString());

                return false;

            } finally {

                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

            }
        }

        Log::warning('Видео не прошло проверку или отсутствует.');
        return false;
    }

    public function hashVideo($video)
    {
        $extension = $video->getClientOriginalExtension();
        return Str::random(40) . '.' . $extension;
    }

    public function makePreview($videoPath, $folder, $visibility = 'public_s3')
    {
        $tempDir = storage_path('app/temp');
        $previewFileName = Str::random(40) . '.jpg';
        $previewPath = $tempDir . '/' . $previewFileName;

        try {
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            // Открываем видео
            $video = $this->ffmpeg->open($videoPath);

            // Извлекаем кадр на 1-й секунде
            $video
                ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(1))
                ->save($previewPath);

            // Загружаем на диск
            $path = Storage::disk($visibility)->putFileAs($folder, new File($previewPath), $previewFileName);

            return Storage::disk($visibility)->url($path);
        } catch (\Exception $e) {
            Log::error('Ошибка создания превью: ' . $e->getMessage());
            return false;
        } finally {
            if (file_exists($previewPath)) {
                unlink($previewPath);
            }
        }
    }
}
