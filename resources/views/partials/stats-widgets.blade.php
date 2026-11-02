<div class="row mb-4">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card border-0 shadow-sm border-start border-danger border-4 h-100">
            <div class="card-body align-items-center row">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Stok Hampir Habis</h6>
                        <h3 class="fw-bold text-danger mb-0">{{ $lowStockCount }} <span class="fs-6 fw-normal text-muted">Item</span></h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 px-3 py-2 rounded-circle border-box">
                        <i class="bi bi-box-seam text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card border-0 shadow-sm border-start border-primary border-4 h-100">
            <div class="card-body align-items-center row">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Penjualan Hari Ini</h6>
                        <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 px-3 py-2 rounded-circle border-box">
                        <i class="bi bi-cart-check text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-start border-warning border-4 h-100">
            <div class="card-body align-items-center row">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Piutan Jatuh Tempo</h6>
                        <h3 class="fw-bold text-warning mb-0">{{ $duePaymentsCount }} <span class="fs-6 fw-normal text-muted">Nota</span></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 px-3 py-2 rounded-circle border-box">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>