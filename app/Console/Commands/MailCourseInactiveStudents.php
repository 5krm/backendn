<?php

namespace App\Console\Commands;

use App\Enums\CourseEmailType;
use App\Enums\CourseStatus;
use App\Jobs\SendCourseEmailJob;
use App\Models\Courses\CourseMail;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonTracking;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:mail-course-inactive-students')]
#[Description('Mailing inactive students over courses')]
class MailCourseInactiveStudents extends Command
{

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mails = $this->getMails();
        $courses = $mails->pluck('course_id');
        $enrollments = Enrollment::whereIn('course_id', $courses)
            ->where('created_at', '<', now()->subDays(3))
            ->with('user')
            ->get();

        foreach ($mails as $mail) {
            $enrolls = $enrollments->where('course_id', $mail->course_id);
            $studentIds = array_unique($this->listCourseMailReceivers($enrolls, $mail));

            foreach ($studentIds as $studentId) {
                $user = $enrolls->where('user_id', $studentId)->first()->user;
                // TO-ASK: should I let the check the alreadySent here too? Point is: is executing a Job that won't do anything will take much??
                if ($mail != null)
                    SendCourseEmailJob::dispatch($user, $mail);
            }
        }
    }

    public function getMails()
    {
        $topMailIds = CourseMail::where('type', CourseEmailType::inactivity)
            ->where('active', true)
            ->select(DB::raw('MIN(id)'))
            ->groupBy('course_id');
        $mails = CourseMail::with('course')
            ->whereIn('id', $topMailIds)
            ->select(['id', 'course_id', 'type'])->get();
        return $mails;
    }

    public function listCourseMailReceivers($enrolls, $mail): array
    {
        $studentIds = [];
        $takenLessons = LessonTracking::where('course_id', $mail->course_id)->get();
        // case 1: student has enrolled 3 days ago to a course however not started any lesson
        $studentIds = $enrolls->whereNotIn('user_id', $takenLessons->pluck('user_id'))->pluck('user_id')->toArray();

        // case 2: student has taken a lesson 3 days ago, however not finished yet
        $notFinishedUsers = $takenLessons->whereNull('completed_at')
            ->where('created_at', '<', now()->subDays(3))
            ->pluck('user_id');
        $studentIds = array_merge($studentIds, $notFinishedUsers->toArray());

        // case 3: student has taken a lesson and finished it (every lesson of the course) however hasn't taken the final exam
        $finishedUsers = $this->getAlmostFinishedUsers($mail);
        $studentIds = array_merge($studentIds, $enrolls->whereNull('passed_at')
            ->whereIn('user_id', $finishedUsers)
            ->pluck('user_id')->toArray());
        return $studentIds;
    }

    function getAlmostFinishedUsers(CourseMail $mail)
    {
        $lessonsNo = Lesson::where('course_id', $mail->course_id)->where('status', CourseStatus::published->value)->count();
        $groupedLessons = DB::table('lesson_trackings')
            ->where('course_id', $mail->course_id)
            ->select('user_id', DB::raw('COUNT(*) as total_lessons'), DB::raw('MAX(completed_at) as last_complete'))
            ->groupBy('user_id')
            ->get();
        $finishedUsers = $groupedLessons->where('total_lessons', $lessonsNo)
            ->where('last_complete', '<', now()->subDays(3))
            ->pluck('user_id');
        return $finishedUsers;
    }
}
