<div class="py-5 ">

    <form wire:submit="save">
        @csrf
        <div class="w-full mb-4 rounded-t-lg">
            <div class="bg-[{{ $this->color }}]  rounded-t-lg p-4">
                <input type="text" placeholder="{{ __('base.title') }}" wire:model.live="title"
                    x-on:reset-note.window="$el.value=''"
                    class="w-full bg-transparent placeholder:italic placeholder:text-slate-900 px-2 focus:outline-none font-bold @error('title') input-error @enderror" />
                @error('title')
                    <div class="label">
                        <span class="label-text-alt text-red-500">{{ $message }} </span>
                    </div>
                @enderror

                <textarea id="note" rows="6" wire:model.live="note"
                    class="w-full mt-4 bg-transparent placeholder:italic placeholder:text-slate-900 px-2 focus:outline-transparent focus:outline-none  @error('note') border-red-500  @enderror text-sm rounded-lg"
                    placeholder="{{ __('notes.write') }}" x-on:reset-note.window="$el.value=''"></textarea>
            </div>
            <div class="flex items-center justify-end p-3 rounded-b-lg border border-t-0 ">

                <div class="dropdown dropdown-bottom me-3 relative z-50">
                    <div tabindex="0" role="button">
                        <li class="  block rounded-full h-6 w-6 bg-[{{ $this->color }}] "></li>
                    </div>
                    <ul class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box">
                        @foreach (App\Models\Lessons\LessonNote::Colors as $color)
                            <button type="button" wire:click="$set('color', '{{ $color }}')"
                                class="mt-1 rounded-full h-6 w-6 bg-[{{ $color }}] ">
                            </button>
                        @endforeach
                    </ul>
                </div>
                <button class="btn btn-primary btn-xs px-5 py-2 h-auto " type="submit">{{ __('base.add') }}</button>

            </div>

            @error('note')
                <div class="label">
                    <span class="label-text-alt text-red-500">{{ $message }} </span>
                </div>
            @enderror
        </div>
    </form>


    <div class="rounded mt-10">

        @foreach ($this->notes as $note)
            <livewire:note-card :$note :key="$note->id" :colors="[]" />
        @endforeach
    </div>

</div>
