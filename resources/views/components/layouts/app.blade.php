@props([
    'title' => null,
    'metaDescription' => null,
    'ogImage' => null,
    'canonical' => null,
    'ogType' => 'website',
])
<x-layouts.main :title="$title ?? null" :metaDescription="$metaDescription ?? null" :ogImage="$ogImage ?? null" :canonical="$canonical ?? null" :ogType="$ogType ?? 'website'">
    <div>
        @auth
            @if (!auth()->user()->hasCompleteProfile())
                <x-alert type="warning">
                    {{ __('profile.profile_incomplete_note') }} <a
                        class="ms-2 inline-flex items-center text-sm font-medium text-yellow-800 hover:underline md:ml-2 "
                        href="{{ route('app.profile') }}"> {{ __('profile.check_out') }}<span
                            class="{{ $direction == 'rtl' ? 'icon-[mdi--arrow-left]' : 'icon-[mdi--arrow-right]' }}"></span></a>
                </x-alert>
            @endif
        @endauth
        <x-navbar />
       
         <main class="container mt-10 flex-grow">
            {{ $slot }}
        </main>
        <x-footer></x-footer>
    </div>
</x-layouts.main>
