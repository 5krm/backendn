<?php

namespace App\Models\Courses;

use App\Enums\CourseEmailType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseMail extends Model
{
    use SoftDeletes;

    protected $casts = [
        'type' => CourseEmailType::class,
        'active' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
