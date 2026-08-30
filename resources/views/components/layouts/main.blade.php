@props([
    'title' => null,
    'metaDescription' => null,
    'ogImage' => null,
    'canonical' => null,
    'ogType' => 'website',
])
<!DOCTYPE html>

<html dir="{{ $direction ?? 'ltr' }}" data-theme="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ isset($title) && $title ? $title . ' | ' . config('app.name') : config('app.name') }}</title>

    {{-- Default SEO Meta Tags --}}
    <meta name="description" content="{{ $metaDescription ?? __('seo.default_description') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title"
        content="{{ isset($title) && $title ? $title . ' | ' . config('app.name') : config('app.name') }}">
    <meta property="og:description" content="{{ $metaDescription ?? __('seo.default_description') }}">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('assets/images/default-course.png') }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) == 'ar' ? 'ar_AR' : 'en_US' }}">

    {{-- LinkedIn (uses og: tags, these reinforce image requirements) --}}
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="627">
    <meta property="og:image:type" content="image/png">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title"
        content="{{ isset($title) && $title ? $title . ' | ' . config('app.name') : config('app.name') }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? __('seo.default_description') }}">
    <meta name="twitter:image" content="{{ $ogImage ?? asset('assets/images/default-course.png') }}">

    {{-- Extra meta slots per page --}}
    @stack('meta')

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-M7JSP3H6');
    </script>
    <!-- End Google Tag Manager -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('app.recaptcha.id') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.4/dist/confetti.browser.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-slate-50 flex flex-col min-h-screen">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M7JSP3H6" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    {{ $slot }}
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/choices.js@11.1.0/public/assets/scripts/choices.min.js"></script>
    <script>
        // Pass single element

        (function(d, t) {
            var BASE_URL = "https://chat.portal365.org";
            var g = d.createElement(t),
                s = d.getElementsByTagName(t)[0];
            g.src = BASE_URL + "/packs/js/sdk.js";
            g.async = true;
            s.parentNode.insertBefore(g, s);
            g.onload = function() {
                window.chatwootSDK.run({
                    websiteToken: 'K11Yoh9P6DUzSpgg1vnZ8Yu5',
                    baseUrl: BASE_URL
                })
            }
        })(document, "script");
    </script>
    @yield('script')
</body>

</html>
