<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use FFMpeg;

class VideoProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('ffmpeg', function () {
            return FFMpeg\FFMpeg::create([
                'ffmpeg.binaries'  => env('FFMPEG_BINARIES', '/usr/bin/ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_BINARIES', '/usr/bin/ffprobe'),
                'timeout'          => env('FFMPEG_TIMEOUT', 3600),
                'ffmpeg.threads'   => env('FFMPEG_THREADS', 12),
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
