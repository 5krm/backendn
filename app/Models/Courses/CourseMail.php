<?php

namespace App\Models\Courses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\CourseEmailType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMail extends Model
{
    use SoftDeletes;

    protected $casts = [
        'type' => CourseEmailType::class,
        'active' => 'boolean'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
