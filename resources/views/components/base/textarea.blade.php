@props([
    'rows' => '3',
    'placeholder' => '',
    'optinal' => false,
    'value' => null,
    'name',
    'label' => '',
    'class' => '',
])

<label class="form-control w-full {{ $class }}">
    <div class="label">
        <span class="label-text">
            {{ $label }}
            @if (!$optinal)
                <span class="text-red-500 font-bold">*</span>
            @endif
        </span>
    </div>
    <textarea name="{{ $name }}" placeholder="{{ $placeholder }}" rows="{{ $rows }}"
        class="textarea textarea-bordered w-full @error($name) input-error @enderror">{{ $value ?: old($name) }}</textarea>
    @error($name)
        <div class="label">
            <span class="label-text-alt text-red-500">{{ $message }} </span>
        </div>
    @enderror
</label>
