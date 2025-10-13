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
    <!-- Select2 Bootstrap Styles -->
    <link href="{{ asset('public/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
    {{-- Print Excel --}}
    <script src="{{ asset('public/js/xlsx.full.min.js') }}"></script>
    <!-- Custom Styles -->
    <link href="{{ asset('public/css/styles.css') }}" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="bg-primaryw expand">
            <div class="d-flex gap-1 justify-content-center pt-4">
                <div class="site-log">
                    <a href="{{ url('/dashboard') }}">
                        <img src="{{ asset('public/img/logo.png') }}" width="50" alt="laescapo-logo">
                    </a>
                </div>
                <div class="sidebar-logo">
                    <a href="{{ url('/dashboard') }}">LA ESCAPO</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="{{ url('/dashboard') }}" class="sidebar-link">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ url('/log-book') }}" class="sidebar-link">
                        <i class="fa fa-book"></i>
                        <span>Visitor's Log Book</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ url('/services') }}" class="sidebar-link">
                        <i class="fa fa-tasks"></i>
                        <span>Visitor's Billing Payments</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ url('/bills') }}" class="sidebar-link">
                        <i class="fa fa-money-bill"></i>
                        <span>Visitor's Bill Summary</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ url('/report') }}" class="sidebar-link">
                        <i class="fa fa-file"></i>
                        <span>Reports</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ url('/profile') }}" class="sidebar-link">
                        <i class="fa fa-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
            </ul>
        </aside>
        <div class="main bg-gradient">
            <nav class="navbar navbar-expand px-4 py-3 bg-dark">
                <div class="navbar-collapse collapse">
                    <button class="toggle-btn" type="button">
                        <i class="fa-solid text-light fa fa-bars fs-5"></i>
                    </button>
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <span class="m-auto me-1 text-light">
                                @auth
                                    {{ auth()->user()->name }}
                                @endauth
                            </span>
                        @endauth
                        <li class="nav-item dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-stat-icon pe-md-0">
                                <i class="text-light fas fa-user-circle avatar"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end rounded animated--fade-in">
                                {{-- <a class="dropdown-item" href="">
                                    <i class="text-success fas fa-user fa-sm fa-fw mr-2"></i>
                                    Profile
                                </a> --}}
                                <a class="dropdown-item" href="{{ url('/logout') }}">
                                    <i class="text-success fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="content px-3 py-4 bg-theme-secondary" id="page-top">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="container-fluid">
                    @yield('content')
                </div>
            </main>
            <footer class="footer bg-dark py-3 shadow text-center">
                <div class="d-flex justify-content-center px-3">
                    <div class="text-light">© 2025 LA ESCAPO. All rights reserved.</div>
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
    {{-- <script src="{{ asset('public/js/fontawesome.min.js') }}"></script> --}}
    <!-- Select2 Script -->
    <script src="{{ asset('public/js/select2.min.js') }}"></script>

    <!-- Print.js JS -->
    <script src="{{ asset('public/js/print.min.js') }}"></script>
    <script src="{{ asset('public/js/html2pdf.bundle.min.js') }}"></script>

    <!--Custom Script -->
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

        const sidebarLinks = document.querySelectorAll('.sidebar-link');

        // Load active link from localStorage
        let activeLink = localStorage.getItem('activeLink');
        let activeItem = null;

        // Remove all active classes first
        sidebarLinks.forEach(item => {
            item.parentElement.classList.remove('active');
        });

        // If activeLink exists, try to find it
        if (activeLink) {
            activeItem = document.querySelector(`.sidebar-link[href="${activeLink}"]`);
        }

        // If no saved link or invalid, default to first link
        if (!activeItem && sidebarLinks.length > 0) {
            activeItem = sidebarLinks[0];
            activeLink = activeItem.getAttribute('href');
            localStorage.setItem('activeLink', activeLink); // store first link
        }

        // Add active class
        if (activeItem) {
            activeItem.parentElement.classList.add('active');
        }

        console.log('Active link:', activeLink);

        // Save active link on click
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                localStorage.setItem('activeLink', link.getAttribute('href'));
            });
        });
    </script>
</body>

</html>
