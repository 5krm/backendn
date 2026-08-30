<?php

namespace App\Jobs;

use DateTimeZone;
use Carbon\Carbon;
use App\Models\Lessons\Lesson;
use App\Youtube\UploadLesson;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class UploadLessonToYoutube implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0; // unlimited

    public function __construct(private Lesson $lesson) {}

    public function handle(): void
    {
        $quota = Cache::get('youtube_upload_quota', 0);
        if ($quota <= 0) {
            // quota reset at midnight Pacific Time
            $tz = new DateTimeZone('America/Los_Angeles');
            $now = Carbon::now($tz);
            $midnight = $now->copy()->endOfDay();
            $delay = $now->diffInSeconds($midnight);

            $this->release($delay);
            return;
        }

        (new UploadLesson)->execute($this->lesson);
        Cache::decrement('youtube_upload_quota');
    }
}
