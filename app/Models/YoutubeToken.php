<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeToken extends Model
{
    protected $casts = [
        'expires_in' => 'datetime',
        'refresh_token_expires_in' => 'datetime',
    ];

    public function needsRefresh(): bool
    {
        // safety buffer, refresh 5 minutes before it actually expires
        return now()->addMinutes(5)->gt($this->expires_in);
    }
}
