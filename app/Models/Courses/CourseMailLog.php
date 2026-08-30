<?php

namespace App\Models\Courses;

use App\Enums\CourseEmailType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CourseMailLog extends Model
{
    protected $fillable = [
        'mail_id',
        'course_id',
        'type',
        'user_id',
        'record_id',
    ];

    protected $guarded = [];

    protected $casts = [
        'type' => CourseEmailType::class,
    ];

    public function mail()
    {
        return $this->belongsTo(CourseMail::class, 'mail_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
