<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SCCA — Usuario externo</title>

    {{-- Estilos globales neutrales --}}
    <style>
        /* Solo estilos comunes que NO interfieran con layouts específicos */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            overflow-x: hidden;
        }

        .badge-notificacion {
            background-color: #ff4d4d;
            color: white;
            border-radius: 50%;
            font-size: 0.7rem;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 8px;
            font-weight: bold;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.3);
            position: relative;
            top: -2px;
        }
    </style>

    {{-- Estilos específicos de la vista --}}
    @stack('styles')
</head>

<body>
    @yield('content')

    {{-- Scripts globales --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script>
        (function() {
            function removeSwal() {
                try {
                    Swal.close();
                } catch (_) {}
                document.querySelectorAll('.swal2-container').forEach(el => el.remove());
            }

            const nav = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
            const cameFromBFCache = !!(nav && nav.type === 'back_forward');

            window.addEventListener('pagehide', removeSwal, {
                capture: true
            });
            window.addEventListener('pageshow', function(e) {
                if (e.persisted || cameFromBFCache) removeSwal();
            });

            const success = @json(session('success'));
            const error = @json(session('error'));
            const errs = @json($errors->any() ? $errors->all() : []);
            const unreadCount = @json($unreadInboxCount ?? 0);

            const isBackForward = cameFromBFCache || (performance.navigation?.type === 2);
            const state = history.state || {};

            function markConsumed() {
                try {
                    history.replaceState({
                        ...state,
                        swalDismissed: true
                    }, '', location.href);
                } catch (_) {}
            }

            if (!isBackForward && !state.swalDismissed) {
                if (success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: success
                    }).then(markConsumed);
                } else if (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error
                    }).then(markConsumed);
                } else if (errs.length) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errs.map(e => `<div>${e}</div>`).join('')
                    }).then(markConsumed);
                } else if (unreadCount > 0) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: `Tienes ${unreadCount} mensaje${unreadCount === 1 ? '' : 's'} nuevo${unreadCount === 1 ? '' : 's'} en tu bandeja de entrada`,
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                }
            } else {
                removeSwal();
            }
        })();
    </script>

    @stack('scripts')
</body>

</html>
