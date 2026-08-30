<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';

    protected $primaryKey = 'email';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'type',
        'created_at',
        'expired_at',
    ];

    // Casts
    protected $casts = [
        'created_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // Check if token is expired
    public function isExpired(int $minutes = 120): bool
    {
        return isset($this->expired_at) ;
        return $this->created_at->lt(Carbon::now()->subMinutes($minutes));
    }

    // Mark token as used (optional if you want)
    public function markAsUsed()
    {
        $this->delete();
    }
}
