<?php

return [
    'notifications' => 'Notifications',
    'type' => 'Type',
    'title' => 'Title',
    'message' => 'Message',
    'received_at' => 'Received',
    'status' => 'Status',
    'mark_as_read' => 'Mark as Read',
    'mark_as_unread' => 'Mark as Unread',
    'mark_all_as_read' => 'Mark All as Read',
    'unread_only' => 'Unread Only',
    'empty' => [
        'heading' => 'No notifications yet',
        'description' => 'When you receive notifications, they will appear here.'
    ],
    'types' => [
        'new_enrollment' => 'New Enrollments',
        'course_completed' => 'Course Completions',
        'new_comment' => 'New Comments',
        'certificate_issued' => 'Certificates Issued',
    ],
    'titles' =>[
        'new_enrollment' => 'New Student Enrollment',
        'course_completed' => 'Course Completed',
        'new_comment' => 'New Comment',
        'certificate_issued' => 'Certificate Issued',
    ],
    'messages'=>[
        'new_enrollment' => ':student_name has enrolled in your course: :course_title',
        'course_completed' => ':student_name has completed your course: :course_title',
        'new_comment' => ':student_name commented on your course: :course_title',
        'certificate_issued' => 'A certificate has been issued for :student_name in course: :course_title',
    ],
    'read' => 'Read'
];
