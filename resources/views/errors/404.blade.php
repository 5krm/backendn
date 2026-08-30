<x-layouts.error>
    <div class="min-h-3/4  grid grid-cols-1 md:grid-cols-2 items-center  ">
        <div class="w-full flex justify-center ">
            <img src="/assets/images/404.png" alt="guide-img" width="450">
        </div>
        <div class="text-center ">
            <p class="font-bold text-9xl md:text-[11rem]  text-[#ccc] flex justify-center items-center mb-3">

                4 <i class="icon-[mdi--alert-circle-outline]"></i> 4</p>

            <p class="font-bold text-xl mb-5">{{ __('errors.not_found') }}</p>
            <p>{{ __('errors.not_found_message') }}</p>

        </div>
    </div>

</x-layouts.error>
