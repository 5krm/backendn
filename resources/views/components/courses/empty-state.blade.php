<div
    class="h-full flex flex-col justify-center items-center border-2 border-dashed border-gray-300 rounded-2xl p-8 bg-gray-50/50 md:w-[320px] md:h-[390px]">    <div class="text-center">
        <div class="bg-gray-100 rounded-full p-4 mb-4 inline-block">
            <i class="icon-[mdi--magnify] size-8 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-700 mb-4">
            {{ __('dashboard.explore') }}
        </h3>
        <a class="btn btn-primary rounded-xl px-8" href="{{ route('courses') }}">{{ __('base.explore') }}</a>
    </div>

</div>
