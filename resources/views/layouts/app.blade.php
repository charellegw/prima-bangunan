<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Prima Bangunan')</title>

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bs-primary: #2563EB;
            --bs-primary-rgb: 37, 99, 235;
            --sidebar-bg: #1E293B;
            --bg-canvas: #F8FAFC;
        }

        body {
            background-color: var(--bg-canvas);
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: #1E293B;
            min-height: 100vh;
        }

        .sidebar-desktop {
            width: 250px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: white;
            position: sticky;
            top: 0;
            flex-shrink: 0;
            z-index: 1020;
        }

        .flex-main-content {
            flex: 1;
            min-width: 0;
            width: calc(100% - 250px);
        }

        @media (max-width: 991.98px) {
            .flex-main-content {
                width: 100%;
            }
        }

        .offcanvas {
            background: var(--sidebar-bg);
            color: white;
            width: 270px !important;
        }

        .nav-link {
            color: #94A3B8;
            transition: all 0.2s ease;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.08);
        }

        .nav-link.active {
            color: #FFFFFF !important;
            background: var(--bs-primary) !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .card-custom {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .card-custom:hover {
            border-color: #CBD5E1;
        }

        .btn-primary-custom {
            background-color: #2563EB;
            border-color: #2563EB;
            color: #FFFFFF;
            font-weight: 600;
            border-radius: 10px;
            padding: 8px 16px;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            background-color: #1D4ED8;
            border-color: #1D4ED8;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        /* Badges */
        .badge-lunas {
            background-color: #DCFCE7;
            color: #15803D;
            border: 1px solid #BBF7D0;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 8px;
        }

        .badge-sebagian {
            background-color: #FEF3C7;
            color: #B45309;
            border: 1px solid #FDE68A;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 8px;
        }

        .badge-belum-bayar {
            background-color: #FEE2E2;
            color: #B91C1C;
            border: 1px solid #FECACA;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 8px;
        }

        .badge-jatuh-tempo {
            background-color: #7F1D1D;
            color: #FEE2E2;
            border: 1px solid #991B1B;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 8px;
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0% { opacity: 1; }
            50% { opacity: 0.85; }
            100% { opacity: 1; }
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #CBD5E1;
            padding: 9px 14px;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar Desktop -->
        <aside class="sidebar-desktop px-3 py-4 d-none d-lg-block">
            @include('partials.sidebar-content')
        </aside>

        <!-- Main Content Area -->
        <div class="flex-main-content d-flex flex-column min-vh-100">
            <!-- Mobile Topbar -->
            <nav class="navbar navbar-expand-lg bg-white border-bottom d-lg-none px-3 py-2 sticky-top shadow-sm">
                <div class="container-fluid p-0">
                    <button class="btn btn-outline-secondary border-0 p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        <i class="bi bi-list fs-3"></i>
                    </button>
                    <span class="fw-bold text-dark ms-2 fs-5">Prima Bangunan</span>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="badge bg-primary-subtle text-primary fw-medium px-2 py-1 rounded-pill">
                            <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name ?? 'Kasir' }}
                        </span>
                    </div>
                </div>
            </nav>

            <!-- Main Body -->
            <main class="p-3 p-md-4 flex-grow-1">
                {{-- Flash Notifications --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Terdapat kesalahan pada input:</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-top py-3 px-4 text-muted small d-flex flex-wrap justify-content-between align-items-center mt-auto">
                <div>&copy; {{ date('Y') }} <strong>Prima Bangunan</strong> — Sistem Manajemen Toko Bahan Bangunan & POS</div>
                <div class="text-secondary">Waktu Sistem: <span class="fw-semibold">{{ date('d M Y, H:i') }}</span> ({{ config('app.timezone', 'Asia/Jakarta') }})</div>
            </footer>
        </div>
    </div>

    <!-- Mobile Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header border-bottom border-secondary border-opacity-25 py-3">
            <h6 class="offcanvas-title text-white fw-bold" id="mobileSidebarLabel">Prima Bangunan</h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            @include('partials.sidebar-content')
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Debounce & Search Optimizer Script -->
    <script>
        function debounce(func, wait = 300) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }
    </script>
    @stack('scripts')
</body>
</html>
