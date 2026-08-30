<?php

namespace App\Models;

use App\Enums\FollowupEmailType;
use Illuminate\Database\Eloquent\Model;

class FollowupEmail extends Model
{
    protected $casts = [
        'sent_at' => 'datetime',
        'email_type' => FollowupEmailType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
