<div class="d-flex align-items-center mb-4 px-2">
    <div class="bg-white text-primary rounded-3 p-2 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
        <i class="bi bi-building-fill fs-5"></i>
    </div>
    <div>
        <h6 class="mb-0 fw-bold text-white lh-sm">Prima Bangunan</h6>
        <small class="text-white-50" style="font-size: 11px;">Bahan Bangunan & Hardware</small>
    </div>
</div>

<ul class="nav flex-column gap-1">
    <li class="nav-item">
        <a href="{{ route('dashboard') }}"
           class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('dashboard') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-grid-1x2-fill me-2 fs-6"></i>
            <span>Beranda</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('item.index') }}"
           class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('item.*') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-box-seam-fill me-2 fs-6"></i>
            <span>Barang & Stok</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('invoices.index') }}"
           class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('invoices.*') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-cart-check-fill me-2 fs-6"></i>
            <span>Kasir / Penjualan</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('invoice-suppliers.index') }}"
           class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('invoice-suppliers.*') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-bag-plus-fill me-2 fs-6"></i>
            <span>Pembelian Supplier</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('payments.index') }}"
           class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('payments.*') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-credit-card-2-front-fill me-2 fs-6"></i>
            <span>Pembayaran & Tagihan</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('shipments.index') }}"
           class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('shipments.*') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-truck me-2 fs-6"></i>
            <span>Pengiriman</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('suppliers.index') }}"
           class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('suppliers.*') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-people-fill me-2 fs-6"></i>
            <span>Data Supplier</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('reports.index') }}"
           class="nav-link d-flex align-items-center px-3 py-2 {{ request()->routeIs('reports.*') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-file-earmark-bar-graph-fill me-2 fs-6"></i>
            <span>Laporan & Rekap</span>
        </a>
    </li>

    <hr class="text-white-50 my-3">

    <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link text-warning-emphasis border-0 bg-transparent w-100 text-start d-flex align-items-center px-3 py-2" style="color: #FDE047 !important;">
                <i class="bi bi-box-arrow-right me-2 fs-6"></i>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </li>
</ul>
