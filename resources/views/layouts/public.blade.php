<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LA ESCAPO</title>
    <!-- Bootstrap Style -->
    <link href="{{ asset('public/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Fontawesome Style -->
    <link href="{{ asset('public/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/fontawesome.min.css') }}" rel="stylesheet">
    <!-- Datatables -->
    <link href="{{ asset('public/css/datatables.min.css') }}" rel="stylesheet">
    <!-- Select2 Style -->
    <link href="{{ asset('public/css/select2.min.css') }}" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="{{ asset('public/css/styles.css') }}" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <div class="main">
            <nav class="navbar-expand bg-dark px-4 py-1 shadow-sm">
                <div class="col-10 m-auto d-flex justify-content-between flex-wrap align-items-center">
                    <a href="{{ url('/') }}" class="">
                        <div class="d-flex flex-wrap align-items-center">
                            <img style="border-radius: 100%;" src="{{ asset('public/img/logo.png') }}" width="60"
                                alt="laescapo-logo">
                            <h5 class="ms-2 mb-0 text-light">LA ESCAPO PORTAL</h5>
                        </div>
                    </a>
                    <div class="d-flex align-items-center gap-5">
                        @if (!auth()->check())
                            <div class="auth-buttons d-flex gap-3">
                                <!-- Button to toggle the Login Offcanvas -->
                                <button class="btn btn-light" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#loginOffcanvas" aria-controls="loginOffcanvas">
                                    <i class="fa fa-circle-user"></i> Login
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </nav>

            @yield('content')

            <footer class="footer py-2 shadow text-center bg-light text-light bg-dark">
                <div class="m-auto">
                    <div class="">© 2025 LA ESCAPO. All rights reserved.</div>
                </div>
            </footer>
        </div>
    </div>

    <!-- jQuery Script -->
    <script src="{{ asset('public/js/jquery.min.js') }}"></script>
    <!-- Bootstrap Script -->
    <script src="{{ asset('public/js/bootstrap.min.js') }}"></script>
    <!-- Datatables -->
    <script src="{{ asset('public/js/datatables.min.js') }}"></script>
    <!-- Fontawesome Script -->
    <script src="{{ asset('public/js/all.min.js') }}"></script>
    <script src="{{ asset('public/js/fontawesome.min.js') }}"></script>
    <!-- Select2 Script -->
    <script src="{{ asset('public/js/select2.min.js') }}"></script>
    <!-- Custom Script -->
    <script src="{{ asset('public/js/script.js') }}"></script>

    <script>
        function hideAlerts(delay = 3000) {
            console.log('Hiding alerts');
            if ($('.alert-success, .alert-danger').length) {
                setTimeout(function() {
                    $('.alert-success, .alert-danger').fadeOut('slow');
                }, delay);
            }
        }
        hideAlerts();

        localStorage.removeItem('activeLink');
    </script>
</body>

</html>
