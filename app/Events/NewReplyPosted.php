<?php

namespace App\Events;

use App\Models\Lessons\LessonComment\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewReplyPosted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Comment $reply)
    {        
        if(!$reply->relationLoaded('lesson')) {
            $reply->load('lesson');
        }
        if(!$reply->relationLoaded('parent')) {
            $reply->load('parent');
        }
    }

}
