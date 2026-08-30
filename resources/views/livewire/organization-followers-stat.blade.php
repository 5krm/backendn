<div class="flex items-center gap-2">
    <!-- <span aria-hidden="true">🔔</span> -->
    <!-- <span class="text-sm text-gray-600">
         {{ __('organization.followers') }} :
    </span> -->
    <span class="text-lg font-semibold text-gray-900">
        {{ number_format($followersCount) }}
    </span>
    <!-- <span class="text-sm text-gray-600">
        ({{ __('organization.followers') }})
    </span> -->
</div>
