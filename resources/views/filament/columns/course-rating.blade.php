@php
    $rating = (float) $getState();
    $fullStars = floor($rating);
@endphp

@if ($getState() !== null)
    <div
        style="display: inline-flex !important; align-items: center !important; gap: 6px !important; white-space: nowrap !important;">
        <div style="display: inline-flex !important; align-items: center !important; gap: 2px !important;">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= $fullStars)
                    <svg style="width: 16px !important; height: 16px !important; color: #f59e0b !important; fill: currentColor !important; display: inline-block !important;"
                        viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @else
                    <svg style="width: 16px !important; height: 16px !important; color: #cbcfd8 !important; fill: currentColor !important; stroke: currentColor !important; stroke-width: 1.5 !important; display: inline-block !important;"
                        viewBox="0 0 20 20">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endif
            @endfor
        </div>

        <span
            style="font-size: 14px !important; font-weight: 700 !important; color: #737475 !important; margin-left: 4px !important; line-height: 1 !important;">
            {{ number_format($rating, 1) }}
        </span>
    </div>
@else
    <span style="color: #6b7280 !important;">-</span>
@endif
