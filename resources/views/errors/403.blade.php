<x-layouts.error>
    <div class="min-h-3/4  grid grid-cols-1 md:grid-cols-2 gap-5 items-center place-items">
        <div class="w-full flex justify-center ">
            <img src="/assets/svg/access_denied.svg" alt="guide-img" height="450">
        </div>
        <div class="text-center px-5">
            <p class="font-bold direction-ltr text-9xl md:text-[11rem]  text-[#ccc] flex justify-center items-center mb-3"
                dir="ltr">

                4 <i class="icon-[mdi--alert-circle-outline]"></i> 3</p>

            <p class="font-bold text-xl mb-5">{{ __('errors.access_denied') }}</p>
            @if ($exception?->getMessage())
                <p class="text-lg">{!! $exception?->getMessage() !!}</p>
            @else
                <p>{{ __('errors.access_denied_message') }}</p>
            @endif
        </div>

    </div>
</x-layouts.error>
