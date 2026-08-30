<x-layouts.error>
    <div class="min-h-3/4  grid grid-cols-1 md:grid-cols-2 gap-5 items-center place-items">
        <div class="w-full flex justify-center ">
            <img src="/assets/images/secure.png" alt="guide-img" width="450">
        </div>
        <div class="text-center ">
            <p class="font-bold text-9xl md:text-[11rem]  text-[#ccc] flex justify-center items-center mb-3">

                <i class="icon-[mdi--key-chain]"></i> !</p>

            <p class="font-bold text-xl mb-5">{{ __('errors.invalid_token') }}</p>
            <p>{{ __('errors.invalid_token_message') }}</p>

        </div>

    </div>

</x-layouts.error>
