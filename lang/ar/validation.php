<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'يجب قبول حقل :attribute.',
    'accepted_if' => 'يجب قبول حقل :attribute عندما يكون :other = :value.',
    'active_url' => 'يجب أن يكون :attribute عنوان URL صالحًا.',
    'after' => 'يجب أن يكون حقل :attribute تاريخًا بعد :date.',
    'after_or_equal' => 'يجب أن يكون حقل :attribute تاريخًا بعد :date أو يساويه.',
    'alpha' => 'يجب أن يحتوي حقل :attribute على أحرف فقط.',
    'alpha_dash' => 'يجب أن يحتوي حقل :attribute على الأحرف والأرقام والشرطات السفلية وعلامات التسطير فقط.',
    'alpha_num' => 'يجب أن يحتوي حقل :attribute على الأحرف والأرقام فقط.',
    'array' => 'يجب أن يكون حقل :attribute مصفوفة',
    'ascii' => 'يجب أن يحتوي حقل :attribute على أحرف أبجدية رقمية أحادية البايت ورموز فقط.',
    'before' => 'يجب أن يكون حقل :attribute تاريخًا قبل :date',
    'before_or_equal' => 'يجب أن يكون حقل :attribute تاريخًا قبل :date أو يساويه.',
    'between' => [
        'array' => 'يجب أن يحتوي حقل :attribute على عدد عناصر بين :min و :max.',
        'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت',
        'numeric' => 'يجب أن يكون حقل :attribute بين :min و :max.',
        'string' => 'يجب أن يكون طول حقل :attribute بين :min و :max حرفًا.',
    ],
    'boolean' => 'يجب أن تكون قيمة حقل :attribute صحيحة أو خاطئة.',
    'can' => 'يحتوي حقل :attribute على قيمة غير مصرح بها.',
    'confirmed' => 'تأكيد حقل :attribute غير متطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'يجب أن يكون حقل :attribute تاريخًا صحيحًا.',
    'date_equals' => 'يجب أن يكون حقل :attribute تاريخًا مساويًا لـ :date.',
    'date_format' => 'يجب أن يطابق حقل :attribute التنسيق :format.',
    'decimal' => 'يجب أن يحتوي حقل :attribute على :decimal خانات عشرية.',
    'declined' => 'يجب رفض حقل :attribute',
    'declined_if' => 'يجب رفض حقل :attribute عندما يكون :other مساويًا لـ :value.',
    'different' => 'يجب أن يكون حقلا :attribute و :other مختلفين.',
    'digits' => 'يجب أن يتكون حقل :attribute من :digits أرقام.',
    'digits_between' => 'يجب أن يتكون حقل :attribute من عدد أرقام بين :min و :max.',
    'dimensions' => 'حقل :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'يحتوي حقل :attribute على قيمة مكررة.',
    'doesnt_end_with' => 'يجب ألا ينتهي حقل :attribute بواحدة من القيم التالية: :values.',
    'doesnt_start_with' => 'يجب ألا يبدأ حقل :attribute بواحدة من القيم التالية: :values.',
    'email' => 'يجب أن يكون حقل :attribute عنوان بريد إلكتروني صحيحًا.',
    'ends_with' => 'يجب أن ينتهي حقل :attribute بواحدة من القيم التالية: :values.',
    'enum' => 'القيمة المحددة لحقل :attribute غير صالحة.',
    'exists' => 'القيمة المحددة لحقل :attribute غير صالحة.',
    'extensions' => 'يجب أن يحتوي حقل :attribute على أحد الامتدادات التالية: :values.',
    'file' => 'يجب أن يكون حقل :attribute ملفًا.',
    'filled' => 'يجب ملء حقل :attribute.',
    'gt' => [
        'array' => 'يجب أن يحتوي حقل :attribute على أكثر من :value عناصر.',
        'file' => 'يجب أن يكون حجم حقل :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute أكبر من :value.',
        'string' => 'يجب أن يحتوي حقل :attribute على أكثر من :value حرفًا.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :value عناصر أو أكثر.',
        'file' => 'يجب أن يكون حجم حقل :attribute أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute أكبر من أو تساوي :value.',
        'string' => 'يجب أن يحتوي حقل :attribute على :value حرفًا أو أكثر.',
    ],
    'hex_color' => 'يجب أن يكون حقل :attribute قيمة لون سداسي عشرية صالحة.',
    'image' => 'يجب أن يكون حقل :attribute صورة.',
    'in' => 'القيمة المحددة لحقل :attribute غير صالحة.',
    'in_array' => 'يجب أن يوجد حقل :attribute في :other.',
    'integer' => 'يجب أن يكون حقل :attribute عددًا صحيحًا.',
    'ip' => 'يجب أن يكون حقل :attribute عنوان IP صحيحًا.',
    'ipv4' => 'يجب أن يكون حقل :attribute عنوان IPv4 صالحًا.',
    'ipv6' => 'يجب أن يكون حقل :attribute عنوان IPv6 صحيحًا.',
    'json' => 'يجب أن يكون حقل :attribute سلسلة JSON صالحة.',
    'lowercase' => 'يجب أن تكون جميع أحرف حقل :attribute أحرفًا صغيرة.',
    'lt' => [
        'array' => 'يجب أن يحتوي حقل :attribute على أقل من :value عناصر.',
        'file' => 'يجب أن يكون حجم حقل :attribute أقل من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute أقل من :value.',
        'string' => 'يجب أن يحتوي حقل :attribute على أقل من :value حرفًا.',
    ],
    'lte' => [
        'array' => 'لا يجب أن يحتوي حقل :attribute على أكثر من :value عناصر.',
        'file' => 'يجب أن يكون حجم حقل :attribute أقل من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute أقل من أو تساوي :value.',
        'string' => 'يجب أن يحتوي حقل :attribute على :value حرفًا أو أقل.',
    ],
    'mac_address' => 'يجب أن يكون حقل :attribute عنوان MAC صالحًا.',
    'max' => [
        'array' => 'لا يمكن أن يحتوي حقل :attribute على أكثر من :max عناصر.',
        'file' => 'لا يجب أن يكون حجم حقل :attribute أكبر من :max كيلوبايت.',
        'numeric' => 'يجب ألا تكون قيمة حقل :attribute أكبر من :max.',
        'string' => 'يجب ألا يزيد طول حقل :attribute عن :max حرفًا.',
    ],
    'max_digits' => 'يجب ألا يتجاوز عدد الأرقام في حقل :attribute :max رقمًا.',
    'mimes' => 'يجب أن يكون حقل :attribute ملفًا من نوع: :values.',
    'mimetypes' => 'يجب أن يكون حقل :attribute ملفًا من نوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :min عنصرًا على الأقل.',
        'file' => 'يجب أن يكون حجم حقل :attribute :min كيلوبايت على الأقل.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute على الأقل :min.',
        'string' => 'يجب أن يكون طول حقل :attribute على الأقل :min حرفًا.',
    ],
    'min_digits' => 'يجب أن يحتوي حقل :attribute على :min رقمًا على الأقل.',
    'missing' => 'يجب ألا يحتوي على حقل :attribute.',
    'missing_if' => 'يجب أن يكون حقل :attribute مفقودًا عندما يكون حقل :other :value.',
    'missing_unless' => 'يجب أن يكون حقل :attribute مفقودًا إلا إذا كانت قيمة حقل :other هي :value.',
    'missing_with' => 'يجب أن يكون حقل :attribute مفقودًا عند وجود :values.',
    'missing_with_all' => 'يجب أن يكون حقل :attribute مفقودًا عند وجود :values.',
    'multiple_of' => 'يجب أن يكون حقل :attribute من مضاعفات :value.',
    'not_in' => 'قيمة حقل :attribute المحددة غير صالحة.',
    'not_regex' => 'تنسيق حقل :attribute غير صالح.',
    'numeric' => 'يجب أن يكون حقل :attribute رقمًا.',
    'password' => [
        'letters' => 'يجب أن يحتوي حقل :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن يحتوي حقل :attribute على حرف واحد كبير وحرف واحد صغير على الأقل.',
        'numbers' => 'يجب أن يحتوي حقل :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن يحتوي حقل :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'تم تسريب :attribute الذي أدخلته. يُرجى اختيار :attribute آخر.',
    ],
    'present' => 'يجب أن يكون حقل :attribute موجودًا.',
    'present_if' => 'يجب أن يكون حقل :attribute موجودًا عندما يكون :other :value.',
    'present_unless' => 'يجب أن يكون حقل :attribute موجودًا ما لم يكن :other :value.',
    'present_with' => 'يجب أن يكون حقل :attribute موجودًا عند وجود :values.',
    'present_with_all' => 'يجب أن يكون حقل :attribute موجودًا عند وجود :values.',
    'prohibited' => 'حقل :attribute محظور.',
    'prohibited_if' => 'حقل :attribute محظور عندما يكون :other :value.',
    'prohibited_unless' => 'حقل :attribute محظور ما لم يكن :other ضمن :values.',
    'prohibits' => 'يمنع حقل :attribute وجود حقل :other.',
    'regex' => 'تنسيق الحقل :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'يجب أن يحتوي حقل :attribute على إدخالات لـ: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_if_accepted' => 'حقل :attribute مطلوب عند قبول :other.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other موجودًا في :values.',
    'required_with' => 'حقل :attribute مطلوب عند وجود :values.',
    'required_with_all' => 'حقل :attribute مطلوب عند وجود :values.',
    'required_without' => 'حقل :attribute مطلوب عندما لا تكون :values موجودة.',
    'required_without_all' => 'حقل :attribute مطلوب في حالة عدم وجود أي من :values.',
    'same' => 'يجب أن يتطابق حقل :attribute مع :other.',
    'size' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :size عناصر.',
        'file' => 'يجب أن يكون حقل :attribute بحجم :size كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute بحجم :size.',
        'string' => 'يجب أن يتكون حقل :attribute من :size أحرف.',
    ],
    'starts_with' => 'يجب أن يبدأ حقل :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون حقل :attribute نصّاً.',
    'timezone' => 'يجب أن يكون حقل :attribute نطاقًا زمنيًا صالحًا.',
    'unique' => 'قيمة :attribute مستخدمة من قبل.',
    'uploaded' => 'فشل تحميل :attribute.',
    'uppercase' => 'يجب أن يكون حقل :attribute بالأحرف الكبيرة.',
    'url' => 'يجب أن يكون حقل :attribute عنوان URL صالحًا.',
    'ulid' => 'يجب أن يكون حقل :attribute ULID صالحًا.',
    'uuid' => 'يجب أن يكون حقل :attribute UUID صالحًا.',
    'profile' => 'إما JPG أو PNG. بحجم 1MB كحد أقصى',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'الاسم',
        'username' => 'اسم المستخدم',
        'email' => 'البريد الإلكتروني',
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'city' => 'المدينة',
        'country' => 'الدولة',
        'address' => 'العنوان',
        'phone' => 'الهاتف',
        'mobile' => 'الجوال',
        'age' => 'العمر',
        'sex' => 'الجنس',
        'gender' => 'النوع',
        'day' => 'اليوم',
        'month' => 'الشهر',
        'year' => 'السنة',
        'hour' => 'الساعة',
        'minute' => 'الدقيقة',
        'second' => 'الثانية',
        'title' => 'العنوان',
        'content' => 'المحتوى',
        'description' => 'الوصف',
        'excerpt' => 'المقتطف',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'available' => 'متاح',
        'size' => 'الحجم',
        'price' => 'السعر',
        'old_price' => 'السعر القديم',
        'slug' => 'الرابط المختصر',
        'bio' => 'السيرة الذاتية',
        'job_title' => 'المسمى الوظيفي',
        'order' => 'الترتيب',
        'duration' => 'المدة',
        'lang' => 'اللغة',
        'status' => 'الحالة',
        'category_id' => 'القسم',
        'author_id' => 'المؤلف',
        'tutor_id' => 'المعلم',
        'course_id' => 'الدورة',
        'lesson_id' => 'الدرس',
        'section_id' => 'القسم',
        'score' => 'الدرجة',
        'is_free' => 'مجاني',
        'video_url' => 'رابط الفيديو',
        'cover' => 'صورة الغلاف',
        'banner' => 'البانر',
        'specialization' => 'التخصص',
        'experience_years' => 'سنوات الخبرة',
        'website' => 'الموقع الإلكتروني',
        'linkedin' => 'لينكد إن',
        'twitter' => 'تويتر',
        'facebook' => 'فيسبوك',
        'instagram' => 'إنستقرام',
        'hourly_rate' => 'السعر بالساعة',
        'qualifications' => 'المؤهلات',
        'languages' => 'اللغات',
        'is_active' => 'نشط',
        'is_verified' => 'موثق',
        'message' => 'الرسالة',
        'subject' => 'الموضوع',
        'image' => 'الصورة',
        'avatar' => 'الصورة الشخصية',
        'file' => 'الملف',
    ],

];
