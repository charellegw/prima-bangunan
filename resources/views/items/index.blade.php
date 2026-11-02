@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')

<div class="container-fluid px-4">

    <!-- HEADER -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <h1 class="m-0 h4 text-dark fw-bold">Daftar Barang</h1>

        <button class="btn btn-primary-soft" data-bs-toggle="modal" data-bs-target="#itemModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Barang
        </button>
    </div>

    <!-- FILTER -->
    <div class="card p-3 mb-4 overflow-visible">
        <div class="row g-3">

            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama barang...">
            </div>

            <div class="col-md-4 position-relative">
                <input type="text" id="categorySearch" class="form-control" placeholder="Pilih / cari kategori...">

                <div id="categoryDropdown"
                     class="list-group position-absolute w-100 mt-1 d-none"
                     style="z-index:1000; max-height:200px; overflow-y:auto;">
                </div>
            </div>

            <div class="col-md-4 position-relative">
                <input type="text" id="brandSearch" class="form-control" placeholder="Pilih / cari brand...">

                <div id="brandDropdown"
                     class="list-group position-absolute w-100 mt-1 d-none"
                     style="z-index:1000; max-height:200px; overflow-y:auto;">
                </div>
            </div>

            <div class="col-md-4">
                <select id="stockFilter" class="form-select">
                    <option value="">Semua Stok</option>
                    <option value="empty">Stock Habis</option>
                    <option value="low">Stock Menipis (&lt;10 pcs)</option>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button onclick="resetFilter()" class="btn btn-secondary-soft border w-100">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Reset Filter
                </button>
            </div>

        </div>
    </div>

    <!-- ITEM GRID -->
    <div class="row g-4" id="itemContainer"></div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="itemModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- Toast --}}
                <div id="formAlert" class="alert d-none py-2 small"></div>

                {{-- Form --}}
                <form id="itemForm" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-3">

                        <!-- REQUIRED -->
                        <div class="col-md-6">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <!-- REQUIRED -->
                        <div class="col-md-6">
                            <label>Brand <span class="text-danger">*</span></label>
                            <input type="text" name="brand" class="form-control" required>
                        </div>

                        <!-- REQUIRED -->
                        <div class="col-md-6">
                            <label>Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="categorySelect" class="form-select" required></select>
                        </div>

                        <!-- OPTIONAL -->
                        <div class="col-md-6">
                            <label>Stock <span class="text-secondary">(opsional)</span></label>
                            <input type="number" name="stock" class="form-control" value="0">
                        </div>

                        <!-- OPTIONAL -->
                        <div class="col-md-6">
                            <label>Harga <span class="text-secondary">(opsional)</span></label>
                            <input type="number" name="price" class="form-control" value="0">
                        </div>

                        <!-- OPTIONAL -->
                        <div class="col-md-6">
                            <label>Harga Reseller <span class="text-secondary">(opsional)</span></label>
                            <input type="number" name="reseller_price" class="form-control" value="0">
                        </div>

                        <!-- OPTIONAL -->
                        <div class="col-12">
                            <label>Deskripsi <span class="text-secondary">(opsional)</span></label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>

                        <!-- OPTIONAL -->
                        <div class="col-12">
                            <label>Gambar <span class="text-secondary">(opsional)</span></label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary-soft" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="submitItem()" class="btn btn-primary-soft">Simpan</button>
            </div>

        </div>
    </div>
</div>

<!-- ================= TOAST ================= -->
<div class="position-fixed top-0 end-0 p-4" style="z-index:9999">
    <div id="successToast" class="toast border-0 shadow-lg">
        <div class="d-flex align-items-center p-3 bg-primary text-white rounded">

            <i class="bi bi-check-circle-fill fs-3 me-3"></i>

            <div>
                <div class="fw-bold fs-5">Berhasil 🎉</div>
                <div>Tambah barang berhasil!</div>
            </div>

        </div>
    </div>
</div>

<script>
let debounceTimer;
let selectedCategory = "";
let selectedBrand = "";
let fetchInterval = null;

/* ================= FETCH ITEMS ================= */
function fetchItems() {
    const search = document.getElementById("searchInput").value;
    const stock = document.getElementById("stockFilter").value;

    fetch(`/api/items?search=${encodeURIComponent(search)}&category=${encodeURIComponent(selectedCategory)}&brand=${encodeURIComponent(selectedBrand)}&stock=${stock}`)
        .then(res => res.json())
        .then(data => renderItems(data))
        .catch(error => console.error('Error fetching items:', error));
}

/* ================= DEBOUNCE ================= */
function debounceFetch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchItems, 500);
}

/* ================= EVENTS ================= */
document.getElementById("searchInput").addEventListener("input", debounceFetch);
document.getElementById("stockFilter").addEventListener("change", fetchItems);
const itemModalElement = document.getElementById('itemModal')

itemModalElement.addEventListener('show.bs.modal', loadCategorySelect);

itemModalElement.addEventListener('hide.bs.modal', function () {
    setTimeout(() => {
        if (document.activeElement) {
            document.activeElement.blur();
        }
    }, 0);
});

// Reset modal saat ditutup
document.getElementById('itemModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('itemForm').reset();
});

/* ================= CATEGORY ================= */
const categoryInput = document.getElementById("categorySearch");
const categoryDropdown = document.getElementById("categoryDropdown");

function loadCategories(keyword = "") {
    fetch(`/api/categories?search=${keyword}`)
        .then(res => res.json())
        .then(data => {
            categoryDropdown.innerHTML = "";

            data.forEach(cat => {
                categoryDropdown.innerHTML += `
                    <button type="button" class="list-group-item list-group-item-action"
                        onclick="selectCategory('${cat.name}')">
                        ${cat.name}
                    </button>
                `;
            });

            categoryDropdown.classList.remove("d-none");
        });
}

categoryInput.addEventListener("focus", () => loadCategories(""));
categoryInput.addEventListener("input", function () {
    loadCategories(this.value);
});

function selectCategory(name) {
    selectedCategory = name;
    categoryInput.value = name;
    categoryDropdown.classList.add("d-none");
    fetchItems();
}

/* ================= BRAND ================= */
const brandInput = document.getElementById("brandSearch");
const brandDropdown = document.getElementById("brandDropdown");

function loadBrands(keyword = "") {
    fetch(`/api/brands?search=${keyword}`)
        .then(res => res.json())
        .then(data => {
            brandDropdown.innerHTML = "";

            data.forEach(brand => {
                brandDropdown.innerHTML += `
                    <button type="button" class="list-group-item list-group-item-action"
                        onclick="selectBrand('${brand}')">
                        ${brand}
                    </button>
                `;
            });

            brandDropdown.classList.remove("d-none");
        });
}

brandInput.addEventListener("focus", () => loadBrands(""));
brandInput.addEventListener("input", function () {
    loadBrands(this.value);
});

function selectBrand(name) {
    selectedBrand = name;
    brandInput.value = name;
    brandDropdown.classList.add("d-none");
    fetchItems();
}

/* ================= CLICK OUTSIDE ================= */
document.addEventListener("click", function (e) {
    if (
        categoryInput && !categoryInput.contains(e.target) &&
        categoryDropdown && !categoryDropdown.contains(e.target) &&
        brandInput && !brandInput.contains(e.target) &&
        brandDropdown && !brandDropdown.contains(e.target)
    ) {
        if(categoryDropdown) categoryDropdown.classList.add("d-none");
        if(brandDropdown) brandDropdown.classList.add("d-none");
    }
});

/* ================= RESET ================= */
function resetFilter() {
    document.getElementById("searchInput").value = "";
    document.getElementById("categorySearch").value = "";
    document.getElementById("brandSearch").value = "";
    document.getElementById("stockFilter").value = "";

    selectedCategory = "";
    selectedBrand = "";

    if(categoryDropdown) categoryDropdown.classList.add("d-none");
    if(brandDropdown) brandDropdown.classList.add("d-none");

    fetchItems();
}

/* ================= RENDER ================= */
function renderItems(items) {
    const container = document.getElementById("itemContainer");
    container.innerHTML = "";

    if (!items || items.length === 0) {
        container.innerHTML = `
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                <p class="mb-0 fw-semibold">Tidak ada data</p>
                <small>Coba ubah filter atau kata pencarian</small>
            </div>
        </div>
        `;
        return;
    }

    items.forEach(item => {
        // Handle image URL dengan lebih baik
        let imageUrl = item.image;
        if (!imageUrl || imageUrl === '' || imageUrl === 'null') {
             imageUrl = '{{ asset('image.png') }}';
        }
        
        // Pastikan URL valid
        if (imageUrl && !imageUrl.startsWith('http') && !imageUrl.startsWith('/')) {
            imageUrl = '/' + imageUrl;
        }

        let stockLabel = "";

        if (item.stock === 0) {
            stockLabel = `
            <div class="stock-status stock-danger">
                <i class="bi bi-x-circle-fill"></i>
                Habis
            </div>`;
        } 
        else if (item.stock < 10) {
            stockLabel = `
            <div class="stock-status stock-warning">
                <i class="bi bi-exclamation-circle-fill"></i>
                Menipis
            </div>`;
        }

        container.innerHTML += `
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card item-card h-100 border-0 shadow-sm position-relative"
                onclick="window.location.href='/items/${item.id}'">

                ${stockLabel}

                <div class="item-image-wrapper">
                    <img src="${imageUrl}" 
                        class="card-img-top item-image"
                        alt="${item.name}"
                        onerror="this.src='{{ asset('image.png') }}'">
                </div>

                <div class="card-body p-3">

                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge badge-soft-primary">
                            ${escapeHtml(item.category)}
                        </span>

                        <span class="badge badge-soft-secondary">
                            ${escapeHtml(item.brand)}
                        </span>
                    </div>


                    <h6 class="fw-semibold mb-2 item-title">
                        ${escapeHtml(item.name)}
                    </h6>


                    <div class="d-flex align-items-center gap-2 mb-2 text-muted item-stock">
                        <span>
                            Stock <b class="text-dark">${item.stock}</b>
                        </span>
                    </div>


                    <div class="item-price">
                        Rp ${formatNumber(item.price)}
                    </div>


                    <div class="item-reseller">
                        Reseller Rp ${formatNumber(item.reseller_price)}
                    </div>

                </div>

            </div>
        </div>
        `;
    });
}

// Helper function untuk format number
function formatNumber(num) {
    if (!num && num !== 0) return '0';
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Helper function untuk prevent XSS
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

/* ================= TOAST ================= */
function showSuccessToast() {
    const toastEl = document.getElementById('successToast');
    const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
    toast.show();
}

/* ================= SUBMIT ================= */
function submitItem() {
    const form = document.getElementById("itemForm");
    const formData = new FormData(form);

    const name = formData.get("name");
    const brand = formData.get("brand");
    const category = formData.get("category_id");

    const alertBox = document.getElementById("formAlert");

    // reset state
    alertBox.classList.add("d-none");
    form.querySelectorAll(".form-control, .form-select").forEach(el => {
        el.classList.remove("is-invalid");
    });

    let errors = [];

    if (!name) {
        errors.push("Nama wajib diisi");
        form.querySelector('[name="name"]').classList.add("is-invalid");
    }

    if (!brand) {
        errors.push("Brand wajib diisi");
        form.querySelector('[name="brand"]').classList.add("is-invalid");
    }

    if (!category) {
        errors.push("Kategori wajib dipilih");
        form.querySelector('[name="category_id"]').classList.add("is-invalid");
    }

    if (errors.length > 0) {
        alertBox.className = "alert alert-danger";
        alertBox.innerHTML = errors.join("<br>");
        alertBox.classList.remove("d-none");
        return;
    }

    const btn = document.querySelector('#itemModal .btn-primary-soft');
    btn.disabled = true;
    btn.innerHTML = "Menyimpan...";

    fetch('/api/items', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async res => {
        let data;

        try {
            data = await res.json();
        } catch (e) {
            throw new Error("Server bukan JSON. Kemungkinan error 500 💀");
        }

        if (!res.ok) {
            throw new Error(data.message || "Validasi gagal");
        }

        return data;
    })
    .then(() => {
        bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
        showSuccessToast();
        form.reset();
        fetchItems();
    })
    .catch(err => {
        console.error(err);

        alertBox.className = "alert alert-danger";
        alertBox.innerText = err.message;
        alertBox.classList.remove("d-none");
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = "Simpan";
    });
}

/* ================= LOAD CATEGORY SELECT ================= */
function loadCategorySelect() {
    fetch('/api/categories')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById("categorySelect");
            if (!select) return;
            
            select.innerHTML = '<option value="">-- pilih kategori --</option>';

            data.forEach(cat => {
                select.innerHTML += `<option value="${cat.id}">${escapeHtml(cat.name)}</option>`;
            });
        })
        .catch(error => console.error('Error loading categories:', error));
}

/* ================= INIT ================= */
document.addEventListener("DOMContentLoaded", () => {
    fetchItems();
});
</script>

@endsection