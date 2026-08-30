@props(['type'])


<div id="banner" tabindex="-1" class="z-50 flex w-full justify-center border border-b border-yellow-300 bg-yellow-50 px-4 py-3 lg:py-4 ">
  <div class="items-center md:flex">
    <p class="text-sm font-medium text-gray-900 md:my-0 ">
        {{ $slot }}
    </p>
  </div>
</div>
