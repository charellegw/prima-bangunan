<div class="row">
    {{-- Stok Hampir Habis --}}
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header bg-white pb-3 px-2 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-dark">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Stok Kritis (Segera Order)
                </h6>
                <a href="#">
                    <button id="toggleEdit" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </button>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nama Barang</th>
                                <th>Kategori</th>
                                <th class="text-center">Sisa</th>
                                <th class="pe-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockItems as $item)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $item->name }}</td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $item->category->name ?? 'N/A' }}</span></td>
                                <td class="text-center text-danger fw-bold">{{ $item->stock }} {{ $item->unit }}</td>
                                <td class="pe-4 text-center">
                                    <a href="{{ route('item.index', $item->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">Lihat</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Semua stok dalam keadaan aman.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Peringatan Bon / Piutang --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white pb-3 px-0">
                <h6 class="m-0 fw-bold text-dark">Peringatan Bon / Piutang</h6>
            </div>
            <div class="card-body">
                @forelse($duePaymentsList as $payment)
                <div class="d-flex align-items-start mb-3">
                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 me-3">
                        <i class="bi bi-person-exclamation fs-5"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold text-dark">{{ $payment->customer_name }}</p>
                        <small class="text-muted">Jatuh tempo: {{ \Carbon\Carbon::parse($payment->due_date)->diffForHumans() }}</small>
                    </div>
                    <div class="ms-auto text-end">
                        <span class="d-block fw-bold text-danger">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @if(!$loop->last) <hr class="text-muted opacity-25"> @endif
                @empty
                <p class="text-muted small text-center">Tidak ada piutang jatuh tempo dalam waktu dekat.</p>
                @endforelse
                
                <button class="btn btn-sm btn-outline-primary w-100 rounded-pill mt-2">Lihat Daftar Piutang</button>
            </div>
        </div>
    </div>
</div>