<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Property Grid') - JorentV2</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/jorent-logo.svg') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Logo Styles Enhanced -->
    <link href="{{ asset('css/jorent-logo-enhanced.css') }}" rel="stylesheet">
    <!-- Iconify Icons -->
    <script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <!-- NoUiSlider CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css" />
    <!-- RemixIcon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    @yield('css')
    
    <style>
        .property-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .bookmark-btn.active {
            background-color: #dc3545 !important;
        }
        .bg-light-subtle {
            background-color: #f8f9fa !important;
        }
        .text-decoration-line-through {
            text-decoration: line-through !important;
        }
        .avatar {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .avatar-sm {
            width: 2rem;
            height: 2rem;
        }
        .avatar-title {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Navigation improvements */
        .navbar-nav.mx-auto {
            text-align: center;
        }
        
        .navbar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            border-radius: 0.375rem;
            margin: 0 0.25rem;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
        
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .navbar-nav .nav-link iconify-icon {
            font-size: 1.1rem;
        }
        
        .navbar-brand {
            transition: all 0.3s ease;
            text-decoration: none !important;
        }
        
        .navbar-brand:hover {
            transform: translateY(-1px);
        }
        
        .navbar-brand .jorent-logo-container {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }
        
        .navbar-brand:hover .jorent-logo-container {
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
        }
        
        @media (max-width: 991.98px) {
            .navbar-nav.mx-auto {
                margin-top: 1rem;
                margin-bottom: 1rem;
            }
            
            .navbar-nav .nav-link {
                margin: 0.25rem 0;
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <x-logo variant="navbar" size="md" :showText="true" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <iconify-icon icon="solar:home-bold-duotone" class="me-1"></iconify-icon>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('property.grid') ? 'active' : '' }}" href="{{ route('property.grid') }}">
                            <iconify-icon icon="solar:buildings-3-bold-duotone" class="me-1"></iconify-icon>
                            Properties
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}" href="{{ route('contracts.index') }}">
                            <iconify-icon icon="solar:document-text-bold-duotone" class="me-1"></iconify-icon>
                            Contracts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin">
                            <iconify-icon icon="solar:settings-bold-duotone" class="me-1"></iconify-icon>
                            Admin Panel
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <iconify-icon icon="solar:login-3-bold" class="me-1"></iconify-icon>
                            Login
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/admin">
                                <iconify-icon icon="solar:settings-bold" class="me-2"></iconify-icon>
                                Admin Login
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <x-logo variant="footer" size="lg" class="mb-3" />
                    <p class="mb-0">&copy; {{ date('Y') }} JorentV2. Professional Real Estate Management.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">All rights reserved.</p>
                    <small class="text-muted">Powered by Jorent System</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Choices.js -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <!-- NoUiSlider -->
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/wnumb@1.2.0/wNumb.min.js"></script>
    
    @yield('script-bottom')
</body>
</html>
