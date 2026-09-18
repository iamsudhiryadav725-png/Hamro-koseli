<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HamroKoseli</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">

    <!-- NProgress -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Reset the guard on every navigation so the NEW page's flash messages are shown.
        document.addEventListener('livewire:navigating', function () {
            window._flashMessagesShown = false;
        });

        // FIX: Read flash data from the <template id="__flash_messages"> in <body>.
        // Livewire only re-renders the body on wire:navigate, not the <head>, so
        // Blade session values placed here in <head> go permanently stale after
        // the first full page load. The body template is always fresh.
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
                        Swal.fire({
                            icon: typeMap[key] || 'info',
                            title: val,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                });
            } catch(e) {}
        }
        document.addEventListener('livewire:navigated', initFlashMessages);
        window.addEventListener('load', initFlashMessages);
    </script>
</head>

<body class="bg-brand-cream overflow-hidden h-screen">

{{-- Flash messages in the body so Livewire re-renders them on every navigation. --}}
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

    <!-- SIDEBAR -->
    <x-user-sidebar />

    <!-- OVERLAY -->
    <div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden">
    </div>

    <!-- MAIN WRAPPER -->
    <div class="flex min-h-screen overflow-x-hidden">

        <!-- CONTENT AREA -->
        <div class="flex-1 md:ml-72 flex flex-col min-w-0 h-screen">

            <!-- TOPBAR (ONLY ONE HEADER) -->
            <x-user-topbar :title="$title ?? 'Dashboard'" />
            <!-- MOBILE SAFE SPACE -->
            <main class="p-4 pt-6 md:px-8 overflow-y-auto flex-1">
                {{ $slot }}
            </main>

        </div>

    </div>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        (function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        })();
        document.addEventListener('livewire:navigated', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
    @livewireScripts
</body>

</html>