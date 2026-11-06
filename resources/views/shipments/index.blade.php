@extends('layouts.app')

@section('title', 'Logistik & Pengiriman — Prima Bangunan')

@section('content')
<div class="d-flex flex-column gap-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="page-title mb-1">Logistik & Status Pengiriman</h1>
            <p class="page-subtitle mb-0">Kelola armada pengantaran barang ke pelanggan dan penerimaan pasokan dari supplier</p>
        </div>
    </div>

    <!-- Filter Card -->
    <x-card :flush="true" class="p-3">
        <form method="GET" action="{{ route('shipments.index') }}" id="filterForm">
            <div class="row g-2 align-items-center">
                <!-- Search Input with Debounce -->
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                            <x-icon name="search" style="width: 16px; height: 16px;" />
                        </span>
                        <input type="text"
                               name="q"
                               id="searchInput"
                               value="{{ request('q') }}"
                               placeholder="Cari no. resi, alamat, pelanggan, supplier..."
                               class="form-control border-start-0 ps-1"
                               autocomplete="off">
                    </div>
                </div>

                <!-- Filter Tipe Pengiriman -->
                <div class="col-6 col-md-3">
                    <select name="type" id="typeFilter" class="form-select">
                        <option value="">Semua Jenis Pengiriman</option>
                        <option value="outgoing" {{ request('type') === 'outgoing' ? 'selected' : '' }}>Kirim ke Pelanggan (Outgoing)</option>
                        <option value="incoming" {{ request('type') === 'incoming' ? 'selected' : '' }}>Masuk dari Supplier (Incoming)</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="col-6 col-md-3">
                    <select name="status" id="statusFilter" class="form-select">
                        <option value="">Semua Status Logistik</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                        <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Shipping (Sedang Jalan)</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered (Sampai)</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned (Retur)</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="col-12 col-md-1 text-end">
                    @if(request()->anyFilled(['q', 'type', 'status']))
                        <x-button href="{{ route('shipments.index') }}" variant="secondary" class="w-100" title="Reset Filter">
                            <x-icon name="x" style="width: 16px; height: 16px;" />
                        </x-button>
                    @endif
                </div>
            </div>
        </form>
    </x-card>

    <!-- Tabel Pengiriman -->
    <x-card :flush="true" class="p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table pb-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No. Resi</th>
                        <th>Tipe</th>
                        <th>Dokumen Terkait</th>
                        <th>Tujuan / Alamat</th>
                        <th>Jadwal Kirim</th>
                        <th>Ongkir</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $sh)
                    <tr>
                        <td class="ps-4 nowrap">
                            <a href="{{ route('shipments.show', $sh->id) }}" class="fw-semibold text-primary text-decoration-none tabular-nums">
                                {{ $sh->shipment_number }}
                            </a>
                        </td>
                        <td class="nowrap">
                            @if($sh->type === 'outgoing')
                                <x-badge variant="info">
                                    <x-icon name="truck" style="width: 12px; height: 12px;" class="me-1" /> Ke Pelanggan
                                </x-badge>
                            @else
                                <x-badge variant="success">
                                    <x-icon name="package" style="width: 12px; height: 12px;" class="me-1" /> Dari Supplier
                                </x-badge>
                            @endif
                        </td>
                        <td>
                            @if($sh->invoice)
                                <a href="{{ route('invoices.show', $sh->invoice->id) }}" class="text-decoration-none small fw-semibold text-dark tabular-nums">
                                    {{ $sh->invoice->invoice_number }}
                                </a>
                                <span class="text-muted d-block small">{{ $sh->invoice->client_name }}</span>
                            @elseif($sh->invoiceSupplier)
                                <a href="{{ route('invoice-suppliers.show', $sh->invoiceSupplier->id) }}" class="text-decoration-none small fw-semibold text-dark tabular-nums">
                                    {{ $sh->invoiceSupplier->invoice_number }}
                                </a>
                                <span class="text-muted d-block small">{{ $sh->invoiceSupplier->supplier->name ?? 'Supplier' }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark small text-truncate" style="max-width: 240px;" title="{{ $sh->address }}">
                                {{ $sh->address }}
                            </div>
                        </td>
                        <td class="nowrap">
                            <span class="text-dark small fw-medium tabular-nums">{{ $sh->date->format('d M Y') }}</span>
                            <span class="text-muted small d-block tabular-nums">{{ $sh->date->format('H:i') }} WIB</span>
                        </td>
                        <td class="nowrap">
                            <span class="small fw-semibold tabular-nums text-dark">Rp {{ number_format($sh->fee, 0, ',', '.') }}</span>
                        </td>
                        <td class="nowrap">
                            @if($sh->status === 'delivered')
                                <x-badge variant="success">
                                    <x-icon name="check" style="width: 12px; height: 12px;" class="me-1" /> Sampai
                                </x-badge>
                            @elseif($sh->status === 'shipping')
                                <x-badge variant="warning">
                                    <x-icon name="truck" style="width: 12px; height: 12px;" class="me-1" /> Di Jalan
                                </x-badge>
                            @elseif($sh->status === 'pending')
                                <x-badge variant="neutral">
                                    <x-icon name="clock" style="width: 12px; height: 12px;" class="me-1" /> Menunggu
                                </x-badge>
                            @else
                                <x-badge variant="danger">
                                    <x-icon name="alert-triangle" style="width: 12px; height: 12px;" class="me-1" /> Retur
                                </x-badge>
                            @endif
                        </td>
                        <td class="pe-4 text-end nowrap">
                            <x-button href="{{ route('shipments.show', $sh->id) }}" variant="outline" size="sm">
                                Detail
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <x-empty-state
                                icon="truck"
                                title="Tidak ada data pengiriman"
                                description="Tidak ada data pengiriman material yang cocok dengan filter pencarian Anda."
                            />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $shipments->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const statusFilter = document.getElementById('statusFilter');
        const filterForm = document.getElementById('filterForm');

        const triggerSearch = debounce(() => {
            filterForm.submit();
        }, 350);

        if (searchInput) searchInput.addEventListener('input', triggerSearch);
        if (typeFilter) typeFilter.addEventListener('change', () => filterForm.submit());
        if (statusFilter) statusFilter.addEventListener('change', () => filterForm.submit());
    });
</script>
@endpush
