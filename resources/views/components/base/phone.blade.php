@props(['countries', 'name', 'optinal' => false, 'label' => '', 'value' => '', 'class' => ''])

<div class="form-control w-full {{ $class }}" x-data="{
    phone_code: '{{ explode(' ', $value)[0] }}',
    phone_number: '{{ explode(' ', $value)[1] ?? '' }}',
    get full_phone() {
        return this.phone_code.value + ' ' + this.phone_number;
    }
}">
    <label class="label">
        <span class="label-text">
            {{ $label }}
            @if (!$optinal)
                <span class="text-red-500 font-bold">*</span>
            @endif
            :
        </span>
    </label>
    <div class="flex gap-2 rtl:flex-row-reverse">
        <div class="w-32">
            <select x-model="phone_code" name="{{ $name }}_code"
                class="select select-bordered  js-choice @error($name) select-error @enderror">
                <option value="">{{ __('base.code') ?? 'Code' }}</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->phone_code }}" @selected(explode(' ', $value)[0] === $country->phone_code)>
                        {{ $country->phone_code }} ({{ $country->code }})
                    </option>
                @endforeach
            </select>
        </div>
        <input type="text" dir="ltr" x-model="phone_number" name="{{ $name }}_number"
            placeholder="0125xxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '')" inputmode="numeric"
            class="input input-bordered flex-1 w-full h-[3.2rem] @error($name) input-error @enderror" />
    </div>
    <input type="hidden" name="{{ $name }}" :value="full_phone">
    @error($name)
        <div class="label">
            <span class="label-text-alt text-red-500">{{ $message }}</span>
        </div>
    @enderror
</div>
