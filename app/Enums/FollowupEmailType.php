<?php

namespace App\Enums;

enum FollowupEmailType: int
{
    case LessonTracking = 1;
    case ExamReminder = 2;
    case CourseSuggestion = 3;
    case CourseRating = 4;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
