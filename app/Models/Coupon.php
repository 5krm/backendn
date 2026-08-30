<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'type', 'value', 'max_uses', 'course_id',
        'starts_at', 'expires_at', 'is_active', 'notes', 'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'starts_at'  => 'datetime',
        'value'      => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    /**
     * Scope for active and non-expired coupons.
     */
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            });
    }
}
