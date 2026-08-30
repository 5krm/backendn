<?php

namespace App\Events;

use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonTracking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LessonTrackingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LessonTracking $tracking;

    public function __construct(Lesson $lesson)
    {
        if (! auth()->check()) {
            return;
        }
        /** @var LessonTracking */
        $tracking = $lesson->load([
            'trackings' => function ($q) {
                $q->where('user_id', auth()->user()->id);
            },
        ])->trackings->first();
        $this->tracking = $tracking;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
