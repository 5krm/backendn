@props(['items', 'optinal' => false, 'value' => null, 'name', 'label' => '', 'class' => ''])
<div class="form-control {{ $class }}">
    <label class="label">
        <span class="label-text">{{ $label }}
            @if (!$optinal)
                <span class="text-red-500 font-bold">*</span>
            @endif
            :
        </span>
    </label>
    <select name="{{ $name }}" class=" select select-bordered w-full js-choice">
        @foreach ($items as $item)
            <option {{ $item->value == $value ? 'selected' : '' }} value="{{ $item->value }}">
                {{ $item->text }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
