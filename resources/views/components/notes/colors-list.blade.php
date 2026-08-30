<div class="dropdown dropdown-bottom me-3">
    <div tabindex="0" role="button">
        <li class="  block rounded-full h-6 w-6 bg-[{{ $this->color }}] "></li>
    </div>
    <ul class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box">
        @foreach (App\Models\Lessons\LessonNote::Colors as $color)
            <button type="button" @click="$wire.update_color($color)"
                class="mt-1 rounded-full h-6 w-6 bg-[{{ $color }}] ">
            </button>
        @endforeach
    </ul>
</div>
