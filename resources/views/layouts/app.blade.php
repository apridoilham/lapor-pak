<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="stylesheet" href="{{ asset('assets/app/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/app/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    @stack('styles')
</head>
<body>
    <div class="main-content">
        @yield('content')
    </div>
    @include('layouts.nav-mobile')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
    @auth
    <script>
        Pusher.logToConsole = true;

        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });

        const channel = pusher.subscribe('private-App.Models.User.{{ Auth::id() }}');

        channel.bind('pusher:subscription_succeeded', function() {
            console.log('Successfully subscribed to private channel!');
        });

        channel.bind('pusher:subscription_error', function(status) {
            console.error('Failed to subscribe to private channel with status:', status);
        });
        
        channel.bind('.report.status.updated', function(data) {
            Swal.fire({
                title: 'Notifikasi Baru!',
                html: data.message,
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });

            const notificationBadge = document.querySelector('.notification-badge');
            if (notificationBadge) {
                notificationBadge.textContent = parseInt(notificationBadge.textContent || '0') + 1;
            } else {
                const notificationIcon = document.querySelector('.nav-notification');
                if (notificationIcon) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'notification-badge';
                    newBadge.textContent = '1';
                    notificationIcon.prepend(newBadge);
                }
            }
        });
    </script>
    @endauth
    @stack('scripts')
</body>
</html>