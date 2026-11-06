<!-- Store Logo & Identity -->
<div class="pb-sidebar-brand">
    <div class="pb-brand-logo">
        <x-icon name="building" size="20" stroke="2" />
    </div>
    <div class="overflow-hidden">
        <div class="fw-bold text-main text-truncate" style="font-size: 15px; letter-spacing: -0.02em;">Prima Bangunan</div>
        <div class="text-secondary small text-truncate" style="font-size: 11.5px;">Bahan Bangunan & POS</div>
    </div>
</div>

<!-- Navigation Links -->
<div class="flex-grow-1 overflow-y-auto">
    <div class="pb-nav-heading">Menu Utama</div>
    <ul class="pb-nav pt-0">
        <li class="pb-nav-item">
            <a href="{{ route('dashboard') }}" 
               class="pb-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <x-icon name="grid" size="18" />
                <span>Beranda</span>
            </a>
        </li>

        <li class="pb-nav-item">
            <a href="{{ route('item.index') }}" 
               class="pb-nav-link {{ request()->routeIs('item.*') ? 'active' : '' }}">
                <x-icon name="box" size="18" />
                <span>Barang & Stok</span>
            </a>
        </li>

        <li class="pb-nav-item">
            <a href="{{ route('invoices.index') }}" 
               class="pb-nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <x-icon name="shopping-cart" size="18" />
                <span>Kasir / Penjualan</span>
            </a>
        </li>

        <li class="pb-nav-item">
            <a href="{{ route('invoice-suppliers.index') }}" 
               class="pb-nav-link {{ request()->routeIs('invoice-suppliers.*') ? 'active' : '' }}">
                <x-icon name="bag-plus" size="18" />
                <span>Pembelian Supplier</span>
            </a>
        </li>

        <li class="pb-nav-item">
            <a href="{{ route('payments.index') }}" 
               class="pb-nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <x-icon name="credit-card" size="18" />
                <span>Pembayaran & Tagihan</span>
            </a>
        </li>

        <li class="pb-nav-item">
            <a href="{{ route('shipments.index') }}" 
               class="pb-nav-link {{ request()->routeIs('shipments.*') ? 'active' : '' }}">
                <x-icon name="truck" size="18" />
                <span>Pengiriman</span>
            </a>
        </li>

        <li class="pb-nav-item">
            <a href="{{ route('suppliers.index') }}" 
               class="pb-nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <x-icon name="users" size="18" />
                <span>Data Supplier</span>
            </a>
        </li>

        <li class="pb-nav-item">
            <a href="{{ route('reports.index') }}" 
               class="pb-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <x-icon name="bar-chart" size="18" />
                <span>Laporan & Rekap</span>
            </a>
        </li>
    </ul>
</div>

<!-- User Profile & Clean Logout (Footer) -->
<div class="pb-sidebar-footer">
    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
        <div class="d-flex align-items-center gap-2 overflow-hidden">
            <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 34px; height: 34px; font-size: 13px;">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
            </div>
            <div class="overflow-hidden">
                <div class="fw-semibold text-main text-truncate" style="font-size: 13px;">
                    {{ auth()->user()->name ?? 'Kasir Toko' }}
                </div>
                <div class="text-secondary small text-truncate" style="font-size: 11px;">
                    {{ auth()->user()->email ?? 'admin@gmail.com' }}
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="pb-logout-btn">
            <x-icon name="log-out" size="16" />
            <span>Keluar (Logout)</span>
        </button>
    </form>
</div>
