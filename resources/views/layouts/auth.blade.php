<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Masuk — Prima Bangunan' }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Single Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Design System -->
    <link rel="stylesheet" href="{{ asset('css/prima-design-system.css') }}">
</head>
<body style="background-color: var(--pb-bg);">

<div class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="row w-100 bg-white rounded-4 shadow-sm border border-light-subtle overflow-hidden" 
         style="max-width: 920px; border-color: var(--pb-border) !important; border-radius: var(--pb-radius-xl) !important;">

        <!-- Left Visual Side (Clean Architectural Accent) -->
        <div class="col-12 col-md-5 p-0 d-none d-md-flex flex-column justify-content-between p-4" 
             style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); color: #FFFFFF;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="pb-brand-logo" style="width: 32px; height: 32px;">
                        <x-icon name="building" size="18" stroke="2" />
                    </div>
                    <span class="fw-bold tracking-tight text-white" style="font-size: 16px;">Prima Bangunan</span>
                </div>
                
                <h4 class="fw-bold text-white mb-2" style="font-size: 22px; line-height: 1.3;">Sistem Kasir & Toko Bahan Bangunan</h4>
                <p class="text-white-50 small mb-0" style="font-size: 13px;">Kelola stok material, transaksi penjualan eceran/grosir, hutang piutang, dan cetak nota kasir secara efisien.</p>
            </div>

            <div class="pt-4 border-top border-white border-opacity-10 text-white-50 small" style="font-size: 11.5px;">
                &copy; {{ date('Y') }} Prima Bangunan. All rights reserved.
            </div>
        </div>

        <!-- Right Form Side -->
        <div class="col-12 col-md-7 p-4 p-lg-5 d-flex flex-column justify-content-center">
            @yield('content')
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
