<div wire:key="rating-component" x-data="{
    rating: @entangle('rating').live,
    hoverRating: 0,
    hasSubmited: false,
    display_course: @entangle('display_course').live,
    previous_rating: @entangle('previous_rating').live,
}" x-on:rating-submitted.window="hasSubmited = true"
    class="w-full max-w-2xl p-6 border bg-white/70 overflow-hidden backdrop-blur-sm {{ $display_course ? ' p-8 border-primary/20 shadow-[0_8px_30px_rgba(0,0,0,0.05)]' : ' border-primary/30' }} rounded-xl text-start mx-auto mt-3 mb-3">
    <div x-show="!hasSubmited">
        <!-- Alpine.js wrapper managing local hover & click states -->
        <div class="space-y-5 text-start">
            <div class="mb-3">
                <h1 class=" text-start text-xl font-semibold capitalize">
                    {{ __('exam.rating.title') }}
                </h1>
                <p class="text-sm text-slate-500">{{ __('exam.rating.caption') }}</p>

            </div>
            @if (isset($previous_rating))
                <div class="py-3 bg-warning/10  border border-warning/20 text-start text-sm rounded-xl px-3">
                    {{__('exam.rating.previous', ['rating' => $previous_rating->rating, 'date' => Carbon\Carbon::parse($previous_rating->updated_at)->format('M d, Y ')])}}
                </div>
            @endif
            <div x-show="display_course"
                class="py-3 bg-emerald-50/40  capitalize border border-primary/20 text-center rounded-xl flex items-center justify-center gap-3">
                <a href="/courses/{{ $course->slug }}" class="!text-primary link text-xl"
                    target="_blank">{{ $course->title }}</a>
            </div>
            <div>

                <h3 class="mb-3">
                    {{ __('exam.rating.ratingQuestion') }}
                </h3>
                <div class="md:flex md:flex-row-reverse justify-between py-1 px-5 mb-1 bg-emerald-50 rounded-lg ">
                    <!-- Star Rating Container -->
                    <div class="flex items-center gap-1.5 text-slate-500  font-medium justify-center">
                        <span x-text="Number(rating).toFixed(1)"> </span>/ 5.0
                    </div>

                    <div class="flex justify-center">
                        <div class="flex items-center gap-1.5" @mouseleave="hoverRating = 0">
                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">

                                <button type="button" @mouseenter="hoverRating = star" @click="rating = star"
                                    class="focus:outline-none transition-transform duration-150 hover:scale-110">
                                    <i class="icon-[mdi--star] text-3xl transition-colors duration-150"
                                        :class="(hoverRating ? star <= hoverRating : star <= rating) ? 'text-emerald-500' :
                                        'text-gray-300'"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="rating > 0">
                <p class="text-start py-2 ps-2 ">
                    {{ __('exam.rating.share') }} <span class="text-slate-400">{{ __('exam.rating.optional') }}</span>
                </p>

                <textarea
                    class="p-3 w-full text-sm text-gray-900 rounded-xl  border border-primary ring-primary focus:ring-0 focus:outline-primary "
                    placeholder="{{ __('comment.action.placeholder') }}" wire:model.live="review" cols="30" rows="7"
                    x-on:form-reset.window="$el.value = ''"></textarea>
            </div>
        </div>


        <div class="flex justify-center">
            <button type="button" wire:click="submitRating"
                class="px-5 py-1  rounded-full btn btn-primary mt-3 text-sm btn-outline ">
                <i wire:loading wire:target="submitRating" class="icon-[mdi--loading] animate-spin text-lg"></i>
                @if (isset($previous_rating))
                    {{ __('exam.rating.edit') }}
                @else
                    {{ __('exam.rating.submit') }}
                @endif
            </button>
        </div>
        <hr class="my-3" />
        <div class=" bg-accent/70 p-3 rounded-xl text-sm text-slate-600">
            💡 {{ __('exam.rating.tip') }}
        </div>
    </div>

    <div x-show="hasSubmited ">
        <!-- Success Icon Circle -->
        <div
            class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-50 text-emerald-600 mb-6 ring-8 ring-emerald-50/50 transition-all duration-300">

            <i class="icon-[mdi--star-shooting-outline]  size-10 transition-colors duration-150"></i>
        </div>

        <h3 class="text-4xl font-bold text-slate-700 tracking-tight mb-3 text-center">
            {{ __('exam.rating.thanks._') }}
        </h3>
        <p class="text-slate-600  leading-relaxed mb-2 text-center">
            {{ __('exam.rating.thanks.message') }}
        </p>
        <p class="text-slate-600  leading-relaxed mb-6 text-center">
            {{ __('exam.rating.thanks.appreciate') }}
        </p>
    </div>
    
        <div class="flex flex-col items-center justify-center">
            {{ $slot }}
        </div>
</div>
