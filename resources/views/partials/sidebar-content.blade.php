<ul class="nav flex-column">

    <li class="nav-item">
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-house-door me-2"></i> Beranda
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('item.index') }}"
           class="nav-link {{ request()->routeIs('item.*') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-box-seam me-2"></i> Barang
        </a>
    </li>

    <li class="nav-item">
        <a href="#"
           class="nav-link {{ request()->routeIs('transaction.*') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-cart me-2"></i> Transaksi
        </a>
    </li>

    <li class="nav-item">
        <a href="#"
           class="nav-link {{ request()->routeIs('invoice.*') ? 'active fw-bold text-white' : '' }}">
            <i class="bi bi-receipt me-2"></i> Nota
        </a>
    </li>

    <hr class="text-white-50">

    <li class="nav-item mt-auto">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link text-warning border-0 bg-transparent w-100 text-start">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
        </form>
    </li>

</ul>