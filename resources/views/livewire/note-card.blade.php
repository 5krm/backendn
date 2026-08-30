<div class="w-full mb-1 group transition-all relative hover:z-50">


    <div class="bg-[{{ $color }}] rounded-t-lg p-4 relative z-10">
        <div class="w-[75%]">
            <input type="text" wire:model.live="title" value="{{ $title ?: old('title') }}"
                class="w-full bg-transparent placeholder:italic placeholder:text-slate-900 px-2 focus:outline-none font-bold @error('title') input-error @enderror"
                @change="$wire.update_note()" @keydown.enter="$wire.update_note()">
            @error('title')
                <div class="label">
                    <span class="label-text-alt text-red-500">{{ $message }} </span>
                </div>
            @enderror
        </div>

        <textarea id="note" rows="5" min-rows="3" @change="$wire.update_note($event.target.value);"
            class="resize-ta w-full mt-4 bg-transparent placeholder:italic placeholder:text-slate-900 px-2 focus:outline-transparent focus:outline-none 
                @error('note') border-red-500  @enderror text-sm rounded-lg"
            placeholder="Write your note...">{{ $content }}</textarea>
        @error('content')
            <div class="label">
                <span class="label-text-alt text-red-500">{{ $message }} </span>
            </div>
        @enderror
    </div>

    <div
        class="flex transition-all duration-200 translate-y-[-55px] h-0 items-center justify-end rounded-b-lg  px-4 py-2 border border-t-0  z-0 relative group-hover:translate-y-0   group-hover:h-auto ">
        <div class="dropdown dropdown-bottom dropdown-hover me-3">
            <div tabindex="0" role="button">
                <li class="  block rounded-full h-6 w-6 bg-[{{ $this->color }}] "></li>
            </div>
            <ul class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box">
                @foreach (App\Models\Lessons\LessonNote::Colors as $color)
                    <button type="button" @click="$wire.update_color('{{ $color }}')"
                        class="mt-1 rounded-full h-6 w-6 bg-[{{ $color }}] ">
                    </button>
                @endforeach
            </ul>
        </div>

        <button class="btn btn-sm btn-error btn-circle" wire:click="$parent.delete({{ $note->id }})" style="min-height:2rem!important;height:2rem;"
            class="w-6 h-6  " aria-label="edit note" role="button">
            <i class="icon-[mdi--times] text-white"></i>
        </button>
    </div>
</div>
