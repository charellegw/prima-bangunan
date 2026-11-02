<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --main-blue: #0D6EFD; }
        body { background-color: #FAFAFA; font-family: 'Inter', sans-serif; }
        .d-flex {
            align-items: flex-start !important;
        }
        .flex-grow-1 {
            min-width: 0;
            width: calc(100% - 260px);
        }
        .container-fluid {
            overflow-x: hidden;
        }
        .row.g-4 {
            margin-right: 0;
            margin-left: 0;
        }
        
        /* Sidebar Desktop */
        .sidebar-desktop { width: 200px; min-height: 100vh; background: var(--main-blue); color: white; position: sticky; top: 0; }
        
        /* Sidebar Offcanvas (Mobile) */
        .offcanvas { background: var(--main-blue); color: white; width: 280px !important; }
        
        .nav-link { color: rgba(255,255,255,0.8); transition: 0.3s; border-radius: 8px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.15); }
        /* .card { 
            border: 1px solid #dee2e6; 
            border-radius: 12px; 
            background: white; 
            padding: 15px; 
            box-sizing: border-box;
            overflow: hidden;
        } */

        .card{
            background:#fff;
            border:none;
            border-radius:24px;
            box-shadow:0 10px 35px rgba(0,0,0,.05);
            padding:24px;
            transition: .25s;
        }

        .info-card{
            background:#F8FAFC;
            border:1px solid #E5E7EB;
            border-radius:16px;
            transition:.2s;
        }

        .info-card:hover{
            background:#F1F5F9;
        }

        .image-wrapper{
            background:#F8FAFC;
            border-radius:24px;
            overflow:hidden;

            aspect-ratio:1/1;

            display:flex;
            align-items:center;
            justify-content:center;

            padding:24px;
        }

        .item-image{
            width:100%;
            height:100%;
            object-fit:contain;
        }

        .border {
                border-color: #eef2f7 !important;
            }

        .bg-light {
            background: #f8fafc !important;
        }

        .card-body h2 {
            letter-spacing: -.03em;
        }

        .card img{
            width:100%;
            height:100%;
            max-height:380px;
            object-fit:cover;
            border-radius:20px;
        }

        .item-card {
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.25s ease;
            background: #ffffff;
            border: 1px solid #F1F5F9 !important;
        }


        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12) !important;
        }


        .item-image-wrapper {
            background: white;
        }

        .stock-status {
            display: flex;
            align-items: center;
            gap: 5px;

            padding: 6px 10px;

            border-radius: 999px;

            font-size: 12px;
            font-weight: 600;

            margin-bottom: 8px;

            backdrop-filter: blur(8px);
        }


        .stock-danger {
            color: #DC2626;
            background: rgba(254, 226, 226, 0.9);
            border: 1px solid #FECACA;
        }
        
        
        .stock-warning {
            color: #D97706;
            background: rgba(254, 243, 199, 0.9);
            border: 1px solid #FDE68A;
        }

        .item-image {
            height: 160px;
            width: 100%;
            object-fit: cover;
            border-radius: 12px;
        }


        .item-title {
            font-size: 18px;
            color: #111827;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }


        .item-stock {
            font-size: 14px;
        }


        .item-price {
            font-size: 20px;
            font-weight: 700;
            color: #2563EB;
            margin-top: 8px;
        }


        .item-reseller {
            font-size: 14px;
            font-weight: 500;
            color: #16A34A;
            margin-top: 4px;
        }

        /* BASE BUTTON */
        .btn-soft {
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
            border-width: 1px;
            cursor: pointer;
        }

        .btn-soft:hover {
            transform: translateY(-1px);
        }

        .btn-soft:active {
            transform: translateY(0);
        }

        .btn-soft:focus,
        .btn-soft:active,
        .btn-soft:focus-visible {
            outline: none !important;
            box-shadow: none !important;
        }


        /* DELETE */
        .btn-delete-hover {
            color: #DC2626;
            background: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.1);
            transition: all 0.2s ease;
        }

        .btn-delete-hover:hover,
        .btn-delete-hover:focus,
        .btn-delete-hover:active {
            color: #B91C1C !important;
            background: rgba(220, 53, 69, 0.18) !important;
            border-color: rgba(220, 53, 69, 0.25) !important;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15) !important;
        }


        /* PRIMARY */
        .btn-primary-soft {
            color: #2563EB;
            background-color: #DBEAFE;
            border-color: #DBEAFE;
        }

        .btn-primary-soft:hover,
        .btn-primary-soft:focus,
        .btn-primary-soft:active {
            color: #1D4ED8 !important;
            background-color: #BFDBFE !important;
            border-color: #BFDBFE !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15) !important;
        }


        /* WARNING */
        .btn-warning-soft {
            color: #D97706;
            background-color: #FEF3C7;
            border-color: #FEF3C7;
        }

        .btn-warning-soft:hover,
        .btn-warning-soft:focus,
        .btn-warning-soft:active {
            color: #B45309 !important;
            background-color: #FDE68A !important;
            border-color: #FDE68A !important;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15) !important;
        }


        /* DANGER */
        .btn-danger-soft {
            color: #DC2626;
            background-color: #FEE2E2;
            border-color: #FEE2E2;
        }

        .btn-danger-soft:hover,
        .btn-danger-soft:focus,
        .btn-danger-soft:active {
            color: #B91C1C !important;
            background-color: #FECACA !important;
            border-color: #FECACA !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15) !important;
        }

        /* SECONDARY */
        .btn-secondary-soft {
            color: #4B5563;
            background-color: #F3F4F6;
            border-color: #F3F4F6;
        }

        .btn-secondary-soft:hover,
        .btn-secondary-soft:focus,
        .btn-secondary-soft:active {
            color: #374151 !important;
            background-color: #E5E7EB !important;
            border-color: #E5E7EB !important;
            box-shadow: 0 4px 12px rgba(75, 85, 99, 0.15) !important;
        }
        /* Success */
        .badge-soft-success {
            background-color: #DCFCE7 !important;
            color: #16A34A !important;
            border: 1px solid #BBF7D0;
            font-weight: 600;
        }

        /* Primary */
        .badge-soft-primary {
            background-color: #DBEAFE !important;
            color: #2563EB !important;
            border: 1px solid #BFDBFE;
            font-weight: 600;
        }

        /* Warning */
        .badge-soft-warning {
            background-color: #FEF3C7 !important;
            color: #F59E0B !important;
            border: 1px solid #FDE68A;
            font-weight: 600;
        }

        /* Danger */
        .badge-soft-danger {
            background-color: #FEE2E2 !important;
            color: #EF4444 !important;
            border: 1px solid #FECACA;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        
        <aside class="sidebar-desktop px-3 py-4 d-none d-lg-block shadow">
            @include('partials.sidebar-content')
        </aside>

        <div class="flex-grow-1">
            <nav class="navbar navbar-expand-lg bg-white border-bottom d-lg-none p-3">
                <div class="container-fluid">
                    <button class="btn-toggle-sidebar" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <span class="fw-bold ms-2">Prima Bangunan</span>
                </div>
            </nav>

            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header border-bottom border-white border-opacity-10">
            <h5 class="offcanvas-title fw-bold" id="mobileSidebarLabel">Prima Bangunan</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            @include('partials.sidebar-content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>