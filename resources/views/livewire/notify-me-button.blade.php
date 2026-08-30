

   <div class=" text-center rounded-2xl p-4 mt-auto bg-gray-50/80 border  border-dashed border-gray-200/50 mb-4"  >
                    <div class="flex-1 flex flex-col items-center   ">
                         <span class=" text-lg font-bold uppercase text-black ">
                                  <span class="text-xl text-white">✨</span>
                                     {{    __('course.coming_soon') }}
                        </span>
            @if($isWishlisted)
               <span class="text-xs text-gray-600    tracking-wider">{{ __('course.notify_msg') }}</span>

                @else
                <span class="text-sm text-gray-600    tracking-wider">{{ __('course.Join_wishlist_to_notified') }}</span>
                <button wire:click="addToWishlist({{ $course['data']->id }})" class="btn btn-primary mt-4  ">
                    {{   __('course.notifyWhenAvailable') }}
                </button>
            @endif
        </div>
    </div>





