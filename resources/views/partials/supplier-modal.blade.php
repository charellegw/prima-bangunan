<div class="modal fade" id="supplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="supplierModalTitle">
                    Supplier
                </h5>

                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="supplierForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>Nama Supplier</label>
                            <input
                                type="text"
                                class="form-control"
                                name="name"
                                id="supplierName"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label>Lokasi</label>
                            <input
                                type="text"
                                class="form-control"
                                name="location"
                                id="supplierLocation">
                        </div>

                        <div class="col-md-6">
                            <label>Harga / PCS</label>
                            <input
                                type="number"
                                class="form-control"
                                name="unit_price"
                                id="supplierUnitPrice"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label>Harga / Lusin</label>
                            <input
                                type="number"
                                class="form-control"
                                name="dozen_price"
                                id="supplierDozenPrice">
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-secondary-soft" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button class="btn btn-primary-soft">
                        Simpan
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>