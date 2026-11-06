<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Prima Bangunan')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Single Consistent Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Master Design System Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/prima-design-system.css') }}">

    @stack('styles')
</head>
<body>
    <div class="d-flex min-vh-100">
        <!-- Sidebar Desktop (Light, Startup Vibe) -->
        <aside class="pb-sidebar d-none d-lg-flex">
            @include('partials.sidebar-content')
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow-1 d-flex flex-column min-w-0" style="background-color: var(--pb-bg);">
            
            <!-- Mobile Topbar Header -->
            <header class="navbar navbar-expand-lg bg-white border-bottom border-light-subtle d-lg-none px-3 py-2 sticky-top shadow-sm" style="border-color: var(--pb-border) !important;">
                <div class="container-fluid p-0">
                    <button class="btn btn-sm btn-outline-secondary border-0 p-1 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-label="Menu">
                        <x-icon name="grid" size="22" />
                    </button>
                    <div class="d-flex align-items-center gap-2">
                        <div class="pb-brand-logo" style="width: 28px; height: 28px;">
                            <x-icon name="building" size="14" stroke="2" />
                        </div>
                        <span class="fw-bold text-main" style="font-size: 15px;">Prima Bangunan</span>
                    </div>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="pb-badge pb-badge-info">
                            {{ auth()->user()->name ?? 'Kasir' }}
                        </span>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Body Content -->
            <main class="flex-grow-1 py-4">
                <div class="app-container">
                    {{-- Soft Flash Notifications --}}
                    @if(session('success'))
                        <x-alert variant="success" class="mb-4">
                            {{ session('success') }}
                        </x-alert>
                    @endif

                    @if(session('error'))
                        <x-alert variant="danger" class="mb-4">
                            {{ session('error') }}
                        </x-alert>
                    @endif

                    @if($errors->any())
                        <x-alert variant="danger" class="mb-4" title="Terdapat beberapa kesalahan:">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </x-alert>
                    @endif

                    {{-- Page Content Slot --}}
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="py-3 px-4 text-center border-top bg-white border-light-subtle" style="border-color: var(--pb-border) !important;">
                <div class="text-secondary small" style="font-size: 12px;">
                    &copy; {{ date('Y') }} Prima Bangunan &bull; Sistem Kasir & Manajemen Toko Bahan Bangunan
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Offcanvas Drawer -->
    <div class="offcanvas offcanvas-start pb-sidebar p-0" tabindex="-1" id="mobileSidebar" style="width: 270px;">
        <div class="d-flex justify-content-end p-2 d-lg-none">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        @include('partials.sidebar-content')
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Helpers: Debounce & Product Image Fallback -->
    <script>
        // Global Debounce Utility for Fast Real-time Search
        function debounce(func, wait = 300) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Global Broken Product Image Fallback Handler
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('img.pb-product-img').forEach(function(img) {
                img.addEventListener('error', function() {
                    const parent = this.parentElement;
                    const placeholder = document.createElement('div');
                    placeholder.className = 'pb-img-placeholder';
                    placeholder.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>`;
                    if (parent) {
                        parent.replaceChild(placeholder, this);
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
