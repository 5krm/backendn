@props([
    'title' => null,
    'metaDescription' => null,
    'ogImage' => null,
    'canonical' => null,
    'ogType' => 'website',
    'fullwidth' => false,
])
<x-layouts.main
    :title="$title ?? null"
    :metaDescription="$metaDescription ?? null"
    :ogImage="$ogImage ?? null"
    :canonical="$canonical ?? null"
    :ogType="$ogType ?? 'website'"
    
>
    <div class="tutor-auth-layout min-h-screen bg-[#eeeefe] flex items-center justify-center">
        {{ $slot }}
    </div>
</x-layouts.main>
