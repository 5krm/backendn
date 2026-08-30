<?php

namespace App\Models;

use App\Enums\SocialPlatform;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialLink extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Attribute<SocialPlatform|null, string|SocialPlatform>
     */
    protected function platform(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? SocialPlatform::tryFrom($value) : null,
            set: function (SocialPlatform|string|null $value) {
                if ($value instanceof SocialPlatform) {
                    return $value->value;
                }

                return $value;
            }
        );
    }

    public function platformLabel(): string
    {
        return $this->platform?->getLabel() ?? ucfirst((string) ($this->attributes['platform'] ?? 'link'));
    }

    public function platformIcon(): string
    {
        return $this->platform?->mdiIcon() ?? 'icon-[mdi--link-variant]';
    }
}
