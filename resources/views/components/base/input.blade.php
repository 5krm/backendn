@props([
    'placeholder' => '',
    'type' => 'text',
    'optinal' => false,
    'value' => null,
    'name',
    'label' => '',
    'dir' => '',
    'disabled' => false,
])


<label class="form-control w-full">
    <div class="label">
        <span class="label-text">
            {{ $label }}
            @if (!$optinal)
                <span class="text-red-500 font-bold">*</span>
            @endif
        </span>
    </div>
    <div class="form-control w-full relative">
        <div class="absolute end-3 top-1/3 hover:cursor-pointer">
            {{ $slot }}
        </div>
        <input {{ $disabled ? 'disabled' : '' }} dir="{{ $dir }}" type="{{ $type }}"
            name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ $value ?: old($name) }}"
            class="input input-bordered w-full @error($name) input-error @enderror" />
    </div>
    @error($name)
        <div class="label">
            <span class="label-text-alt text-red-500">{{ $message }} </span>
        </div>
    @enderror

</label>
