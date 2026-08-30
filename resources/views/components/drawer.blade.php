@props(['title', 'id', 'position' => 'start-0'])
<div id="{{ $id }}" class="lesson-side-menu  max-w-[20%]">
    <div
        class="lesson-side-label toggle-side-menu items-center justify-center lg:w-5 w-0 cursor-pointer bg-slate-100 h-screen sticky top-0 px-7">
        <div class="-rotate-90 ">
            <h1 class="text-lg font-bold text-center">
                {{ $title }}
            </h1>
        </div>
    </div>
    <div
        class="lesson-side-menu-details overflow-y-auto p-4 bg-slate-100 lg:sticky fixed top-0  {{ $position }} z-50 h-screen">
        <ul class="min-h-full">
            <h1 class="text-xl font-bold flex items-center justify-between">
                {{ $title }}
                <button class="btn btn-sm btn-ghost toggle-side-menu"><i class="icon-[mdi--menu]"></i></button>
            </h1>
            <div>
                {{ $slot }}
            </div>
        </ul>
    </div>
</div>
