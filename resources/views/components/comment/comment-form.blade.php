<form class="mb-6" wire:submit="{{ $method }}">
    <div class="py-2 px-4 mb-4 bg-white rounded-lg rounded-t-lg border border-gray-200">
        <label for="{{ $inputId }}" class="sr-only text-gray-900">{{ $inputLabel }}</label>
        <textarea id="{{ $inputId }}"
            class="px-0 w-full text-sm text-gray-900 border-0 focus:ring-0 focus:outline-none @error($state) border-red-500 @enderror"
            placeholder="{{__('comment.action.placeholder')}}" wire:model="{{ $state }}" cols="30" rows="7"
            x-on:comment-form-reset.window="$el.value = ''"></textarea>
        @error($state)
        <p class="mt-2 text-sm text-red-600">
            {{ $message }}
        </p>
        @enderror
    </div>

    <button type="submit" class="btn btn-sm btn-primary">
        {{__('comment.action.post')}}
    </button>
</form>