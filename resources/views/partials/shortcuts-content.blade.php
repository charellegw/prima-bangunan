<div class="">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="m-0" style="font-weight: 600;">Menu</h5>
        <button id="toggleEdit" class="btn btn-sm btn-outline-primary">
            Edit Menu
        </button>
    </div>

    <div class="row g-4" id="menuContainer"></div>
</div>

{{-- Modal --}}
<div class="modal fade" id="menuModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      
      <div class="modal-header">
        <h6 class="modal-title fw-bold">Tambah Menu</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <label class="form-label small">Pilih Menu</label>
        <select id="menuSelect" class="form-select">
          <option value="">-- pilih menu --</option>
          <option value="Stok Barang|bi-box-seam">Stok Barang</option>
          <option value="Buat Nota|bi-receipt">Buat Nota</option>
          <option value="Keuangan|bi-currency-dollar">Keuangan</option>
          <option value="Laporan|bi-graph-up">Laporan</option>
        </select>

        <!-- preview -->
        <div id="previewMenu" class="mt-3 text-center d-none">
          <i id="previewIcon" class="bi fs-1 text-primary"></i>
          <p id="previewName" class="small fw-bold text-primary mb-0"></p>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-sm btn-primary" onclick="confirmAddMenu()">Tambah</button>
      </div>

    </div>
  </div>
</div>

<script>
const menuContainer = document.getElementById("menuContainer");

let editMode = false;

let menus = JSON.parse(localStorage.getItem("user_menus")) || [
    { name: "Stok Barang", icon: "bi-box-seam text-primary" },
    { name: "Buat Nota", icon: "bi-receipt text-primary" },
    { name: "Keuangan", icon: "bi-currency-dollar text-primary" }
];

function saveToLocalStorage() {
    localStorage.setItem("user_menus", JSON.stringify(menus));
}

/* RENDER CARD */
function renderMenuCard(name, icon, index) {
    return `
    <div class="col-6 col-sm-4 col-md-3 col-lg-2 p-2">
        <div class="position-relative card d-flex flex-column justify-content-center align-items-center text-center w-100"
             style="aspect-ratio:1/1; cursor:pointer; padding:0.5rem; overflow:hidden;">

            <i class="bi ${icon} text-primary fs-2 mb-1"></i>

            <p class="small fw-semibold mb-0 text-primary text-truncate w-100" style="font-size:1rem;">
                ${name}
            </p>

            <span onclick="event.stopPropagation(); deleteMenu(${index})"
                  class="btn-delete-hover position-absolute top-0 end-0 mt-2 me-2 px-2 py-0 small border border-danger text-danger rounded-3"
                  style="display:none; font-size:1rem;">
                <i class="bi bi-x"></i>
            </span>
        </div>
    </div>
    `;
}

/* 🔹 RENDER */
function renderMenus() {
    menuContainer.innerHTML = "";

    menus.forEach((menu, index) => {
        menuContainer.innerHTML += renderMenuCard(menu.name, menu.icon, index);
    });

    if (editMode) {
        menuContainer.innerHTML += `
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 p-2">
                <div 
                    onclick="addMenu()"
                    class="position-relative border border-2 border-secondary border-opacity-50 rounded-4 d-flex flex-column justify-content-center align-items-center text-secondary text-center w-100"
                    style="aspect-ratio: 1/1; border-style: dashed !important; cursor: pointer; padding: 0.5rem !important; overflow: hidden;"
                >
                    <i class="bi bi-plus-lg fs-2 fs-md-1 mb-1"></i>
                    <p class="small fw-bold mb-0" style="font-size: 0.75rem;">Tambah Menu</p>
                </div>
            </div>
        `;
    }

    toggleDelete();
}

/* 🔹 EDIT MODE */
document.getElementById("toggleEdit").addEventListener("click", () => {
    editMode = !editMode;
    document.getElementById("toggleEdit").innerText = editMode ? "Selesai" : "Edit Menu";
    renderMenus();
});

/* 🔹 SHOW DELETE */
function toggleDelete() {
    document.querySelectorAll(".position-absolute").forEach(el => {
        el.style.display = editMode ? "block" : "none";
    });
}

/* DELETE */
function deleteMenu(index) {
    menus.splice(index, 1);
    saveToLocalStorage();
    renderMenus();
}

/* OPEN MODAL */
function addMenu() {
    new bootstrap.Modal(document.getElementById('menuModal')).show();
}

/* PREVIEW */
const menuSelect = document.getElementById("menuSelect");
const preview = document.getElementById("previewMenu");
const previewIcon = document.getElementById("previewIcon");
const previewName = document.getElementById("previewName");

menuSelect.addEventListener("change", function () {
    if (!this.value) {
        preview.classList.add("d-none");
        return;
    }

    const [name, icon] = this.value.split("|");

    preview.classList.remove("d-none");
    previewIcon.className = `bi ${icon} fs-1 text-primary`;
    previewName.innerText = name;
});

/* CONFIRM ADD */
function confirmAddMenu() {
    const value = menuSelect.value;
    if (!value) return;

    const [name, icon] = value.split("|");

    menus.push({ 
        name: name, 
        icon: icon + " text-primary" 
    });

    saveToLocalStorage();
    renderMenus();

    menuSelect.value = "";
    preview.classList.add("d-none");
    bootstrap.Modal.getInstance(document.getElementById('menuModal')).hide();
}

renderMenus();
</script>