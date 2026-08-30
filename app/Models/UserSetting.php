<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'language',
        'notifications_enabled',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
