@props(['value' => null, 'name', 'label' => '', 'size' => 'md'])
<div class="form-control">
    <label class="cursor-pointer label">
        <span class="label-text   pe-2">{{ $label }} </span>
        <input value="1" name="{{ $name }}" type="checkbox" {{ $value == '1' ? 'checked' : '' }}
            class="checkbox checkbox-{{$size}} checkbox-primary" />
    </label>
    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
