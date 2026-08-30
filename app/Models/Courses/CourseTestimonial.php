<?php

namespace App\Models\Courses;

use App\Models\Courses\Course;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CourseTestimonial extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }


    /**
     * @return Attribute<string, never>
     */
    protected function AuthorImage(): Attribute
    {
        $file = $this->getMedia('authors')
        ->last()
        ?->getUrl() ??
        URL::to('/') . '/assets/images/default-user.png';
        
        return Attribute::make(
            get: fn () => $file
        );
    }
}
