@extends('layouts.app')

@section('content')
<div class="container">

    {{-- ITEM HEADER --}}
    <div class="card border-0 rounded-4 overflow-hidden mb-4">
        <div class="row g-0">

            @php
                use Illuminate\Support\Facades\Storage;

                $image = $item->image_url
                    ? Storage::url($item->image_url)
                    : asset('image.png');
            @endphp

            {{-- IMAGE --}}
            <div class="col-lg-4">
                <div class="image-wrapper">
                    <img
                        src="{{ $image }}"
                        alt="{{ $item->name }}"
                        class="item-image"
                    >
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="col-lg-8">
                <div class="card-body p-4 p-lg-5">

                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">

                        <div>

                            <h2 class="fw-bold mb-2">
                                {{ $item->name }}
                            </h2>

                            @if ($item->stock == 0)
                                <span class="badge badge-soft-danger">
                                    Stok Habis
                                </span>
                            @elseif ($item->stock < 10)
                                <span class="badge badge-soft-warning">
                                    Stok Menipis
                                </span>
                            @else
                                <span class="badge badge-soft-success">
                                    Stok Aman
                                </span>
                            @endif

                        </div>

                        <div class="text-lg-end">

                            <div class="text-muted small">
                                Harga Jual
                            </div>

                            <div class="fs-2 fw-bold">
                                Rp {{ number_format($item->price,0,',','.') }}
                            </div>

                            <div class="text-muted small mt-2">
                                Harga Reseller
                            </div>

                            <div class="fs-5 fw-semibold text-success">
                                Rp {{ number_format($item->reseller_price,0,',','.') }}
                            </div>

                        </div>

                    </div>

                    {{-- INFO --}}
                    <div class="row g-3 mt-4">

                        <div class="col-md-4">
                            <div class="info-card p-3 h-100">
                                <small class="text-muted d-block mb-1">
                                    Brand
                                </small>

                                <div class="fw-semibold">
                                    {{ $item->brand }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-card p-3 h-100">
                                <small class="text-muted d-block mb-1">
                                    Category
                                </small>

                                <div class="fw-semibold">
                                    {{ $item->category->name }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-card p-3 h-100">
                                <small class="text-muted d-block mb-1">
                                    Stock
                                </small>

                                <div class="fw-semibold">
                                    {{ $item->stock }} pcs
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="mt-5">

                        <h6 class="fw-bold mb-3">
                            Deskripsi
                        </h6>

                        <p class="text-secondary mb-0">
                            {{ $item->description }}
                        </p>

                    </div>

                    {{-- BUTTON --}}
                    <div class="d-flex flex-column flex-md-row gap-3 mt-5">

                        <a class="btn btn-primary-soft flex-fill py-3 rounded-3">
                            Edit Item
                        </a>

                        <a class="btn btn-danger-soft flex-fill py-3 rounded-3">
                            Hapus Item
                        </a>

                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- TABLE WRAPPER (biar bisa scroll kalau lokasi banyak) --}}
    <div class="card border-primary">
        <div class="card-body">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 fw-medium">
                    Harga Beli Supplier
                </h6>

                <button class="btn btn-primary-soft btn-sm"
                        onclick="createSupplier()">
                    + Tambah Supplier
                </button>
            </div>

            {{-- NO DATA --}}
            @if($suppliers->count() == 0)
                <div class="col-12 text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                        <p class="mb-0 fw-semibold">Tidak ada data harga beli dari supplier</p>
                        <small>Silahkan masukkan data supplier terlebih dahulu</small>
                    </div>
                </div>
            @else

            {{-- HARGA SUPPLIER --}}
            <div class="table-responsive overflow-hidden">
                {{-- TABLE --}}
                <table class="table align-middle text-center">
                    <thead style="text-transform: uppercase;">
                        <tr>
                            <th class="text-start fw-medium">Supplier</th>

                            @foreach ($locations as $location)
                                <th class="fw-medium">{{ $location }}</th>
                            @endforeach

                            <th class="fw-medium">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                {{-- SUPPLIER --}}
                                <td class="text-start">
                                    {{ $supplier->name }}

                                    <div class="small text-muted">
                                        {{ $supplier->location ?? 'No Location' }}
                                    </div>
                                </td>

                                {{-- PRICES --}}
                                @foreach($locations as $location)
                                    <td>
                                        @php
                                            $price = $prices[$supplier->id][$location] ?? null;
                                        @endphp

                                        @if($price)
                                            <div class="fw-bold text-primary">
                                                {{ number_format($price['pcs'], 0, ',', '.') }}/pcs
                                            </div>
                                            <div class="small text-success">
                                                {{ number_format($price['lusin'], 0, ',', '.') }}/lusin
                                            </div>
                                        @else
                                            <span class="text-danger fw-bold">-</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button
                                            class="btn btn-warning-soft btn-sm"
                                            onclick="editSupplier(this)"
                                            data-id="{{ $supplier->id }}"
                                            data-name="{{ $supplier->name }}"
                                            data-location="{{ $supplier->location }}"
                                            data-unit="{{ optional($supplier->supplierPrices->first())->unit_price }}"
                                            data-dozen="{{ optional($supplier->supplierPrices->first())->dozen_price }}">
                                            Edit
                                        </button>

                                        <button class="btn btn-danger-soft btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteSupplierModal{{ $supplier->id }}">

                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- SUPPLIER DELETE MODAL --}}
                @foreach ($suppliers as $supplier)
                    @include('partials.delete-modal', [
                        'modalId' => 'deleteSupplierModal'.$supplier->id,
                        'title' => 'Hapus Supplier?',
                        'subtitle' => 'Supplier <strong>'.$supplier->name.'</strong> akan dihapus secara permanen.',
                        'action' => route('supplier.price.destroy', [$item->id, $supplier->id]),
                    ])
                @endforeach
            </div>

            @endif

            {{-- SUPPLIER MODAL --}}
            @include('partials.supplier-modal')
            <input
                type="hidden"
                id="updateSupplierRoute"
                value="{{ route('supplier.price.update', [$item->id, '__SUPPLIER__']) }}">
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const deleteModal = document.getElementById('deleteModal');

    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function () {
            console.log("");
        });
    }
});

let supplierModal;

document.addEventListener('DOMContentLoaded', function () {
    const supplierModalElement = document.getElementById('supplierModal');

    supplierModal = new bootstrap.Modal(supplierModalElement);

    supplierModalElement.addEventListener('hide.bs.modal', function () {
        setTimeout(() => {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        }, 0);
    });
});
    
function createSupplier() {

    document.getElementById('supplierModalTitle').innerText = 'Tambah Supplier';

    document.getElementById('supplierForm').action =
        "{{ route('supplier.price.store', $item->id) }}";

    document.getElementById('formMethod').value = "POST";

    document.getElementById('supplierName').value = "";
    document.getElementById('supplierLocation').value = "";
    document.getElementById('supplierUnitPrice').value = "";
    document.getElementById('supplierDozenPrice').value = "";

    supplierModal.show();
}

function editSupplier(button) {
    console.log(button);

    document.getElementById('supplierModalTitle').innerText = 'Edit Supplier';

    const url = document
        .getElementById('updateSupplierRoute')
        .value
        .replace('__SUPPLIER__', button.dataset.id);

    document.getElementById('supplierForm').action = url;

    document.getElementById('formMethod').value = "PATCH";

    document.getElementById('supplierName').value = button.dataset.name;
    document.getElementById('supplierLocation').value = button.dataset.location;
    document.getElementById('supplierUnitPrice').value = button.dataset.unit;
    document.getElementById('supplierDozenPrice').value = button.dataset.dozen;

    supplierModal.show();
}
</script>
@endsection