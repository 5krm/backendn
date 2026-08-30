<?php

namespace App\Models;

use App\Models\Courses\Course;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Organization extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'category',
        'founded',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'founded' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function followers(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'organization_followers')
            ->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile();
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $media = $this->getFirstMedia('logo');
                $default = URL::to('/').'/assets/images/default-org-logo.png';

                if ($media) {
                    return $media->getUrl();
                }

                return $default;
            }
        );
    }

    protected function logoPath(): Attribute
    {
        return Attribute::make(
            get: function () {
                $media = $this->getFirstMedia('logo');
                $default = public_path('/assets/images/default-org-logo.png');
                if ($media) {
                    return $media->getUrl();
                }

                return $default;
            }
        );
    }

    protected function stampPath(): Attribute
    {
        return Attribute::make(
            get: function () {
                $media = $this->getFirstMedia('stamp');
                $default = public_path('assets/images/signature.png');

                if ($media) {
                    return $media->getUrl();
                }

                return $default;
            }
        );
    }

    protected function stampUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $media = $this->getFirstMedia('stamp');
                $default = URL::to('/').'/assets/images/signature.png';

                if ($media != null) {
                    return $media->getUrl();
                }

                return $default;
            }
        );
    }
}
