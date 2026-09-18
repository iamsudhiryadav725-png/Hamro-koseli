@props([
    'title'       => 'Hamro Koseli – Gifts & Surprises Delivered in Nepal',
    'description' => 'Hamro Koseli is Nepal\'s trusted gifting platform. Send gifts, sweets, and surprises to your loved ones across Nepal.',
    'ogImage' => '/images/og-images.jpg',
])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <!-- SEO Meta -->
    <meta name="description" content="{{ $description }}">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:title"       content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image"       content="{{ asset($ogImage) }}">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image"       content="{{ asset($ogImage) }}">
    <link rel="icon" type="image/png" href="{{ asset('images/Simplified logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- NProgress -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

    {{--
        FIX 1: window globals are set once on first load here in <head>.
        FIX 2: On every wire:navigate, Livewire re-renders the page body but NOT the <head>.
                So we ALSO update these globals inside the livewire:navigated event below
                using a hidden <template> in the <body> that carries the fresh server values.
    --}}
    <script>
        window.isLoggedIn       = @json(auth()->check());
        window.loginUrl         = @json(route('userlogin'));
        window.initialCartCount = @json(auth()->check() ? (int) \App\Models\Cart::where('user_id', auth()->id())->sum('quantity') : 0);
        window.cartAddUrl       = @json(route('cart.add'));
    </script>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        [wire\:navigating] {
            opacity: 0.5;
            transition: opacity 0.3s;
        }
    </style>

    <script>
        // Reset the guard on every navigation so the NEW page's flash messages are shown.
        document.addEventListener('livewire:navigating', function () {
            window._flashMessagesShown = false;
        });

        // FIX: Read flash data from the <template id="__flash_messages"> in <body>.
        // Livewire re-renders the body on every navigation so the template always
        // carries fresh session values — unlike this <head> block which is only
        // evaluated once on the very first full page load.
        function initFlashMessages() {
            if (window._flashMessagesShown) return;
            window._flashMessagesShown = true;

            const tpl = document.getElementById('__flash_messages');
            if (!tpl) return;
            try {
                const msgs = JSON.parse(tpl.dataset.messages);
                const typeMap = {
                    success:          'success',
                    error:            'error',
                    status:           'success',
                    info:             'info',
                    warning:          'warning',
                    password_success: 'success',
                };
                Object.entries(msgs).forEach(([key, val]) => {
                    if (val) {
                        const toastType = typeMap[key] || 'info';
                        if (typeof window.showToast === 'function') {
                            window.showToast(val, toastType);
                        } else if (window.Swal) {
                            Swal.fire({
                                icon: toastType,
                                title: val,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                customClass: { popup: 'swal-hamrokoseli-toast' },
                            });
                        }
                    }
                });
            } catch(e) {}
        }

        document.addEventListener('livewire:navigated', function () {
            // FIX 2 (continued): After Livewire swaps the page body, read the
            // fresh server values from the hidden <template> tag rendered in <body>
            // and update window globals so JS always has the correct auth state.
            const tpl = document.getElementById('__page_globals');
            if (tpl) {
                try {
                    const data = JSON.parse(tpl.dataset.globals);
                    window.isLoggedIn       = data.isLoggedIn;
                    window.initialCartCount = data.initialCartCount;
                    window.cartAddUrl       = data.cartAddUrl;
                    window.loginUrl         = data.loginUrl;
                } catch(e) {}
            }

            // FIX 4: Refresh the CSRF token meta tag so POST requests (add to cart,
            // wishlist toggle, reviews) don't get 419 errors after navigation.
            const tplCsrf = document.getElementById('__csrf_token');
            if (tplCsrf) {
                const freshToken = tplCsrf.dataset.token;
                const metaCsrf   = document.querySelector('meta[name="csrf-token"]');
                if (metaCsrf && freshToken) metaCsrf.setAttribute('content', freshToken);
            }

            initFlashMessages();
        });

        window.addEventListener('load', initFlashMessages);
    </script>
</head>
<body>

@php
    $pageGlobals = [
        'isLoggedIn' => auth()->check(),
        'initialCartCount' => auth()->check()
            ? (int) \App\Models\Cart::where('user_id', auth()->id())->sum('quantity')
            : 0,
        'cartAddUrl' => route('cart.add'),
        'loginUrl' => route('userlogin'),
    ];
@endphp

{{-- Page globals refreshed on every Livewire navigation --}}
<template
    id="__page_globals"
    data-globals='@json($pageGlobals)'>
</template>

{{-- Fresh CSRF token for AJAX requests after Livewire navigation --}}
<template
    id="__csrf_token"
    data-token="{{ csrf_token() }}">
</template>

{{-- Flash messages in the body so Livewire re-renders them on every navigation.
     The <head> is only rendered once on the first full page load, so flash data
     placed there goes stale. This template is swapped with the body on each
     wire:navigate, giving initFlashMessages() fresh session values every time. --}}
@php
    $flashMessages = json_encode([
        'success'          => session('success'),
        'error'            => session('error'),
        'status'           => session('status'),
        'info'             => session('info'),
        'warning'          => session('warning'),
        'password_success' => session('password_success'),
    ]);
@endphp
<template
    id="__flash_messages"
    data-messages='{{ $flashMessages }}'>
</template>

<x-frontend-header />

{{ $slot }}

<x-frontend-footer />
<x-login-modal />
<x-chatbot />

@livewireScripts

</body>
</html>