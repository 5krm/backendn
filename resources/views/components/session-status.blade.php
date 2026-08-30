@props(['status'])

@if ($status)
<div class="flex justify-between p-4 mb-4 bg-green-100 rounded-lg " role="alert">
    <div class="text-sm font-medium text-green-700 ">
        {{ $status }}
    </div>
</div>
@endif