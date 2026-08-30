@php
    $hour = now()->hour;
    $greeting = match (true) {
        $hour >= 5 && $hour < 12 => __('student.greeting.morning'),
        $hour >= 12 && $hour < 17 => __('student.greeting.afternoon'),
        $hour >= 17 && $hour < 22 => __('student.greeting.evening'),
        default => __('student.greeting.hello'),
    };
@endphp
<div class="bg-primary/5 rounded-xl p-5 text-start my-3 border border-primary/20">
    <div class="rounded-full bg-primary/15 w-fit py-1 px-4 flex gap-3 text-primary text-sm mb-2 font-semibold">
        <i class="icon-[mdi--creation-outline] size-4"></i>{{__('student.greeting.learning_space')}}
    </div>
    <h1 class="text-3xl font-semibold">{{ $greeting }}, {{ $user->name }}! 👋</h1>
    <p class="font-light mt-3 text-slate-500">{{__('student.greeting.encouragement', ['courses' => $inProgress])}}🌱
</div>
