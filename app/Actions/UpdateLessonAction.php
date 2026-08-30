<?php

namespace App\Actions;

use Exception;
use App\Models\Lessons\Lesson;
use App\Data\Lessons\LessonData;
use Vimeo\Laravel\Facades\Vimeo;

class UpdateLessonAction
{
    public function execute(Lesson $lesson, LessonData $data): ?Lesson
    {
        $lesson->update($data->except('video_id')->toArray());
        if ($data->video_id && $data->video_id != $lesson->video_id) {

            $videoInfo = $this->getVideoInfo($data->video_id);
            if (!$videoInfo) return null;

            [$html, $duration] = $videoInfo;

            $oldDuration = $lesson->duration;
            $lesson->video_html = $html;
            $lesson->video_id = $data->video_id;
            $lesson->duration = $duration;

            $lesson->courseSection()->increment('duration', $duration - $oldDuration);
            $lesson->course()->increment('duration', $duration - $oldDuration);
            $lesson->save();
        }

        return $lesson;
    }

    private function getVideoInfo(string $video_id): ?array
    {
        try {
            $response = Vimeo::request("/videos/{$video_id}", ['fields' => ['embed', 'duration']]);
            $html = $response['body']['embed']['html'];
            $duration = $response['body']['duration'];

            return [$html, $duration];
        } catch (Exception) {
            return null;
        }
    }
}
