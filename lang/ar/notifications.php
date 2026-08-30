<?php

return [
    'notifications' => 'الإشعارات',
    'title' => 'الإشعار',
    'type' => 'النوع',
    'message' => 'الرسالة',
    'received_at' => 'تاريخ الاستلام',
    'status' => 'الحالة',
    'mark_as_read' => 'تعيين كمقروء',
    'mark_as_unread' => 'تعيين كغير مقروء',
    'mark_all_as_read' => 'تعيين الكل كمقروء',
    'unread_only' => 'عرض غير المقروء',
    'empty' => [
        'heading' => 'لا توجد إشعارات بعد',
        'description' => 'عندما تتلقى إشعارات، ستظهر هنا.'
    ],
    'types' => [
        'new_enrollment' => 'التسجيلات الجديدة',
        'course_completed' => 'الدورات المكتملة',
        'new_comment' => 'التعليقات الجديدة',
        'certificate_issued' => 'الشهادات الصادرة',
    ],
    'titles' =>[
        'new_enrollment' => 'تسجيل طالب جديد',
        'course_completed' => 'تم إكمال دورة',
        'new_comment' => 'تعليق جديد',
        'certificate_issued' => 'شهادة صادرة',
    ],
    'messages'=>[
        'new_enrollment' => ':student_name قد سجّل في الدورة: :course_title',
        'course_completed' => ':student_name قد أنهى الدورة: :course_title',
        'new_comment' => ':student_name ترك تعليقاً على الدورة: :course_title',
        'certificate_issued' => 'تم إصدار شهادة لـ :student_name في الدورة: :course_title',
    ],
    'read' => 'مقروء'
];
