@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $logo = public_path('assets/images/ngo-academy-logo-en.png');
@endphp

<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@700" rel="stylesheet">
</head>

<body>
    <div>
        <div class="p-3 certificate-container">
            <div class="p-3 certificate-border h-full w-full ">
                <table class="w-full certificate-header">
                    <tr>
                        <td style="min-width:30%">
                            <img src="{{ $logo }}" alt="guide-img" style="width:200px; height:auto">
                        </td>
                        <td style="min-width:300px"></td>

                        @if ($tutor['org_logo'])
                            <td style="min-width:30%;text-align: {{ $isRtl ? 'right' : 'left' }};">
                                <img src="{{ $tutor['org_logo'] }}" style="width:200px; height:auto">
                            </td>
                        @endif
                    </tr>
                </table>
                <div class="certificate-body">
                    <h1 class="capitalize text-lg my-2 ar-text">
                        {{ __('course.certificate.title') }}
                    </h1>
                    <p class="capitalize text-sm  my-2 ar-text">
                        {{ __('course.certificate.certify') }}
                    </p>
                    <h1 class="username ar-text">{{ $user->name }}</h1>
                    <span
                        class="ar-text  text-sm">{{ __('course.certificate.completed_hours', ['hours' => $hours]) }}</span>
                    <h3 class="ar-text text-lg">{{ $title }}</h3>
                </div>

                <table class="footer " style="width:100%;">
                    <tr>
                        <td class="text-xl text-start pt-5 font-family" style="min-width:350px">
                            <p class="ar-text text-sm text-grey" style="margin-bottom: 0">
                                {{ __('course.certificate.issue_date') }}</p>
                            <span class="ar-text text-xs">{{ $date }}</span>
                        </td>
                        <td class="text-xl text-center">
                            <img src="{{ $tutor['stamp'] }}" style="width:100px; height:auto">
                            <p class="ar-text text-sm text-grey" style="margin-bottom: 0">
                                {{ __('course.certificate.tutor') }}</p>
                            <span class="ar-text">{{ $tutor['name'] }}</span>
                        </td>
                        <td class="text-xl pt-5 text-end font-family" style="width:40%">
                            <p class="ar-text text-sm text-grey" style="margin-bottom: 0">
                                {{ __('course.certificate.number') }}</p>
                            <span class="ar-text text-xs">{{ $credentialId }}</span>
                            <p class="ar-text text-sm text-grey verify-label">{{ __('course.certificate.verify_at') }}
                            </p>
                            <span class="ar-text verify-url text-xs">{{ $verificationUrl }}</span>


                        </td>
                    </tr>
                </table>

            </div>

        </div>
    </div>

</body>

</html>
<style>
    * {
        font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    }

    .certificate-header p.subtitle {
        margin: -7px {{ $isRtl ? '0px' : '40px' }} 0px {{ $isRtl ? '40px' : '0px' }};
        font-size: .8rem;
    }

    .certificate-title {
        font-size: 5.5rem;
        font-style: italic;
        display: inline;
        margin: 0px;
        font-family: "Tangerine", cursive;
    }

    p {
        margin: 0px;
        padding: 0px;
    }

    .pb-10 {
        padding-bottom: 2rem;
    }

    .p-3 {
        padding: 0.75rem;
    }

    .certificate-border {
        border: 10px solid black;
        background-image: url({{ public_path('assets/images/certificate-background.png') }});
    }

    .h-full {
        height: 100%;
    }

    .pt-5 {
        padding-top: 4rem;
    }

    .w-full {
        width: 100%;
    }

    .p-3 {
        padding: 0.75rem;
    }

    .certificate-container {
        height: 615px;
        width: 960px;
        border: none;
    }

    .text-end {
        text-align: end;
        text-align: right;
    }

    .text-start {
        text-align: end;
        text-align: left;
    }

    .text-grey {
        color: #6b7280;
    }

    .capitalize {
        text-transform: capitalize;
    }

    .certificate-body {
        text-align: center;
        font-size: 1.1rem;
    }

    html[dir="rtl"] .certificate-body,
    html[dir="rtl"] .footer {
        direction: rtl;
    }

    .ar-text {
        font-family: "tajawal", sans-serif;
    }

    .username {
        color: #00cc99;
        font-weight: bold;
        font-size: 2.25rem;
        margin: 2px;
    }

    .text-lg {
        font-size: 1.3rem;
    }

    .text-sm {
        font-size: 0.8rem;
    }

    .text-xs {
        font-size: 0.6rem;
    }

    .footer {
        text-align: center;
        font-size: 1.2rem;
        padding-top: 3.5rem;
    }
</style>
