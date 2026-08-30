<?php

namespace App\Youtube;

use App\Models\Lessons\Lesson;
use Google\Http\MediaFileUpload;
use Google\Service\YouTube\Video;
use Google\Service\YouTube\VideoSnippet;
use Google\Service\YouTube\VideoStatus;

class UploadLesson
{
    public function execute(Lesson $lesson): void
    {
        // This is a long running process,
        // so max execution time is set to 0 (unlimited)
        ini_set('max_execution_time', 0);

        [$youtube, $client] = YoutubeClient::getClient();

        $video = $this->createVideo($lesson);
        $client->setDefer(true);
        /** @var mixed */
        $insertRequest = $youtube->videos->insert('status,snippet', $video);

        // Define Chunk Size (Must be multiples of 256 KB)
        $chunkSizeBytes = 5 * 1024 * 1024; // 5MB chunk
        $mediaUpload = new MediaFileUpload(
            $client,
            $insertRequest,
            'video/*',
            null,
            true,
            $chunkSizeBytes
        );

        $filePath = public_path('uploads/'.$lesson->video_path);
        $mediaUpload->setFileSize(filesize($filePath));

        $status = false;
        $handle = fopen($filePath, 'rb');
        while (! $status && ! feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $mediaUpload->nextChunk($chunk);

            echo 'chunk uploaded ';
        }

        fclose($handle);
        $client->setDefer(false);

        if ($status instanceof Video) {
            $lesson->video_id = $status->getId();
            $lesson->is_ready = true;
            $lesson->save();

            unlink($filePath);
        }
    }

    private function createVideo(Lesson $lesson): Video
    {
        $video = new Video;
        $snippet = new VideoSnippet;
        $snippet->setTitle($lesson->title);

        // Education category.
        $snippet->setCategoryId('27');
        $video->setSnippet($snippet);

        $status = new VideoStatus;
        $status->setPrivacyStatus(VideoStatus::PRIVACY_STATUS_unlisted);
        $video->setStatus($status);

        return $video;
    }
}
