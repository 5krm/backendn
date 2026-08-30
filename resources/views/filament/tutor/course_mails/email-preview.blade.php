<div class="border rounded-lg p-4">
    <p><strong>{{__('course.emails.fields.subject')}}:</strong> {{$get('subject')}}</p>

    <p class="mt-2">
        {{ str_replace(
            ['{student_name}', '{course_name}'],
            ['{student_name}', $get('course')?->title],''
        ) }}
    </p>

    <hr class="my-4">
    <div class="mt-5 py-3" style="margin-top:1rem;">
        {!! str_replace(
        [
            '{course_name}',
            '{course_url}',
            '{tutor_name}',
            '{tutor_email}',
            '{student_name}',
        ],
        [
            
            $course?->title,
            $course?route('courses.details', ['course' => $course->slug]):'',
            $tutor?->name,
            $tutor?->email,
            'Fatima'
        ],
        $get('body')
    ) !!}
    </div>

</div>