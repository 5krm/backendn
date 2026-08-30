<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorCommunicationsApiController extends Controller
{
    use ApiResponse;

    public function comments(Request $request): JsonResponse
    {
        $status = $request->query('status', 'all');

        $comments = [
            [
                'id' => 'c1',
                'student_name' => 'Layla Mansour',
                'course_title' => 'React Fundamentals',
                'lesson_title' => 'useState vs useReducer Architecture',
                'comment' => 'In lesson 4, why do we pass the lazy initializer function to useReducer instead of calling the function directly inside the component body?',
                'time' => '20m ago',
                'status' => 'unreplied',
                'replies' => [],
            ],
            [
                'id' => 'c2',
                'student_name' => 'Omar Khaled',
                'course_title' => 'Python for Data Science',
                'lesson_title' => 'Pandas DataFrame Data Cleaning',
                'comment' => 'Should we use .loc or .iloc when filtering missing NaN rows in large datasets for optimal memory performance?',
                'time' => '2h ago',
                'status' => 'unreplied',
                'replies' => [],
            ],
            [
                'id' => 'c3',
                'student_name' => 'Noor Ahmadi',
                'course_title' => 'UX Design 101',
                'lesson_title' => 'Wireframing & Typography',
                'comment' => 'Is 16px the industry standard body font size for mobile responsive layouts to avoid auto-zoom on iOS input fields?',
                'time' => '1d ago',
                'status' => 'resolved',
                'replies' => [
                    [
                        'tutor_name' => 'Akram Salah',
                        'text' => 'Yes, exactly Noor! 16px prevents iOS Safari from automatically zooming in when students focus on form text inputs.',
                        'time' => '18h ago',
                    ],
                ],
            ],
        ];

        if ($status !== 'all') {
            $comments = array_values(array_filter($comments, fn ($c) => $c['status'] === $status));
        }

        return $this->success($comments);
    }

    public function replyComment(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'reply' => ['required', 'string'],
        ]);

        return $this->success([
            'comment_id' => $id,
            'reply' => $data['reply'],
            'status' => 'resolved',
        ], 'Reply submitted successfully');
    }

    public function threads(Request $request): JsonResponse
    {
        $threads = [
            [
                'id' => 't1',
                'student_name' => 'Layla Mansour',
                'course_title' => 'React Fundamentals',
                'unread' => 2,
                'last_message' => 'Thank you for explaining useReducer! Quick question on line 42...',
                'time' => '10m ago',
                'status' => 'online',
                'messages' => [
                    ['sender' => 'student', 'text' => "Hi Akram! I'm trying to implement the custom hook from Module 2.", 'time' => '10:15 AM'],
                    ['sender' => 'tutor', 'text' => 'Hello Layla! Make sure you return the state object along with the dispatch function.', 'time' => '10:20 AM'],
                    ['sender' => 'student', 'text' => 'Thank you for explaining useReducer! Quick question on line 42 of the demo repo, should we pass the initial state as lazy initializer?', 'time' => '10:30 AM'],
                ],
            ],
            [
                'id' => 't2',
                'student_name' => 'Omar Khaled',
                'course_title' => 'Python for Data Science',
                'unread' => 0,
                'last_message' => 'Got it! The pandas dataframe assignment is completed.',
                'time' => '2h ago',
                'status' => 'offline',
                'messages' => [
                    ['sender' => 'student', 'text' => 'Hi tutor, I submitted assignment 3 for Pandas dataframes.', 'time' => '08:12 AM'],
                    ['sender' => 'tutor', 'text' => 'Great work Omar! I will review it this afternoon.', 'time' => '08:45 AM'],
                ],
            ],
        ];

        return $this->success($threads);
    }

    public function sendMessage(Request $request, $threadId): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        return $this->success([
            'thread_id' => $threadId,
            'message' => $data['message'],
            'time' => now()->format('h:i A'),
            'sender' => 'tutor',
        ], 'Message sent successfully');
    }

    public function broadcast(Request $request): JsonResponse
    {
        $data = $request->validate([
            'course_id' => ['required'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        return $this->success([
            'broadcast_id' => 'BC-'.rand(1000, 9999),
            'subject' => $data['subject'],
            'recipients' => 412,
        ], 'Broadcast announcement dispatched to enrolled students');
    }

    // ── AI Tutor Co-Pilot Endpoint ──────────────────────────────────────────

    public function aiGenerate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'in:course_outline,lesson_content,quiz_generator,reply_draft'],
            'topic' => ['required', 'string'],
            'lang' => ['nullable', 'in:ar,en'],
        ]);

        $mode = $data['mode'];
        $topic = $data['topic'];
        $lang = $data['lang'] ?? 'ar';

        $generated = '';

        if ($mode === 'course_outline') {
            $generated = $lang === 'ar'
                ? "### 📘 مخطط الدورة التدريبية: {$topic}\n\n**الوصف العام:**\nدورة شاملة ومتقدمة تهدف إلى تزويد الطلاب بالمهارات العملية والخبرة التطبيقية في {$topic} مع إبراز أفضل الممارسات البرمجية والمعمارية.\n\n**أهداف التعلم الرئيسية:**\n• فهم وإتقان المبادئ الأساسية والهيكلية في {$topic}.\n• بناء مشاريع عمل واقعية واحترافية جاهزة للانطلاق.\n• تطبيق آليات الأمان والأداء العالي والتخزين المؤقت.\n\n**المتطلبات السابقة المقترحة:**\n• إلمام بأساسيات البرمجة والهياكل البيانات.\n• معرفة سابقة بأدوات الويب الأساسية."
                : "### 📘 Course Outline: {$topic}\n\n**Course Summary:**\nAn advanced, hands-on masterclass designed to empower students with production-ready skills and deep expertise in {$topic}.\n\n**Key Learning Objectives:**\n• Master fundamental principles and scalable architecture for {$topic}.\n• Build real-world production applications with industry best practices.\n• Implement performance optimization, caching, and state management.\n\n**Recommended Prerequisites:**\n• Solid foundation in programming concepts.\n• Familiarity with modern web standards.";
        } elseif ($mode === 'lesson_content') {
            $generated = $lang === 'ar'
                ? "### 📝 محتوى الدرس المقترح: {$topic}\n\n#### 1. المقدمة والأهداف العامة (5 دقائق)\nفي هذا الدرس سوف نتعرف على {$topic} وكيفية استخدامه لحل المشكلات المعقدة في المشاريع الحقيقية.\n\n#### 2. الشرح النظري والأمثلة البرمجية (15 دقيقة)\n- المفهوم الأساسي ودورة الحياة.\n- مقارنة الحل التقني بالبدائل المتاحة.\n- كتابة نموذج كود نظيف وتطبيقي.\n\n#### 3. التنبيهات الشائعة وأفضل الممارسات (10 دقائق)\n- تجنب الأخطاء الشائعة في إدارة الأداء.\n- كتابة اختبارات وحدة لضمان الجودة."
                : "### 📝 Suggested Lesson Plan: {$topic}\n\n#### 1. Introduction & Overview (5 mins)\nIn this lesson, we will cover the core concepts of {$topic} and how it solves real-world engineering bottlenecks.\n\n#### 2. Deep Dive & Live Coding (15 mins)\n- Architecture overview and lifecycle patterns.\n- Comparative analysis against alternative patterns.\n- Step-by-step code implementation demo.\n\n#### 3. Common Pitfalls & Best Practices (10 mins)\n- Avoiding performance memory leaks.\n- Unit testing strategies for seamless deployment.";
        } elseif ($mode === 'quiz_generator') {
            $generated = $lang === 'ar'
                ? "### ❓ أسئلة اختبار مقترحة: {$topic}\n\n**السؤال 1:** ما هي الميزة الرئيسية لاستخدام {$topic} في التطبيقات واسعة النطاق؟\n- [ ] زيادة استهلاك الذاكرة\n- [x] تحسين قابلية الصيانة والأداء العالي\n- [ ] تقليل عدد الاختبارات\n- [ ] تعطيل التخزين المؤقت\n\n**السؤال 2:** متى يفضل تفادي هذا النمط؟\n- [x] في النماذج البسيطة جداً التي لا تتطلب تعقيداً إضافياً\n- [ ] في المشاريع الإنتاجية الكبرى"
                : "### ❓ Assessment Questions: {$topic}\n\n**Question 1:** What is the primary architectural benefit of utilizing {$topic} in production?\n- [ ] Increased memory consumption\n- [x] Superior maintainability and high concurrency throughput\n- [ ] Eliminating test suites\n- [ ] Disabling browser cache\n\n**Question 2:** When is it recommended to avoid this pattern?\n- [x] For simple static prototypes where boilerplate adds unnecessary overhead\n- [ ] In large enterprise systems";
        } elseif ($mode === 'reply_draft') {
            $generated = $lang === 'ar'
                ? "مرحباً بك! شكراً لسؤالك المميز حول {$topic}.\n\nالسبب الأساسي يعود إلى أن المحرك البرمجي يقوم بتحسين استدعاء الدوال عند تمرير المعاملات كدالة مرجعية، مما يمنع إعادة التنفيذ غير الضرورية مع كل دورة تحديث للواجهة.\n\nأتمنى أن يكون هذا الشرح واضحاً، ولا تتردد في طرح أي استفسار آخر!"
                : "Hello! Thank you for the great question regarding {$topic}.\n\nThe main rationale is that the engine optimizes initialization by executing callback references lazily rather than repeatedly invoking constructor calls on every render pass.\n\nLet me know if you would like me to provide a code sandbox example!";
        }

        return $this->success([
            'result' => $generated,
            'mode' => $mode,
            'topic' => $topic,
        ]);
    }
}
