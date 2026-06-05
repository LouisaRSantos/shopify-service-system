<div class="main-panel" id="ajax-content">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="card-title mb-1">Import Customers</h4>
                                <p class="card-description text-muted mb-0">Upload a CSV file to import customers into Shopify.</p>
                            </div>
                            <span class="badge bg-soft-success text-success">Bulk upload</span>
                        </div>

                        <div class="alert alert-info py-2">
                            <i class="mdi mdi-information-outline me-2"></i>
                            Use the template to keep column order correct and reduce import errors.
                        </div>

                        <form id="importForm" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group mb-4">
                                <label class="form-label">CSV File</label>
                                <input type="file" name="file" accept=".csv" class="form-control form-control-lg" required>
                                <small class="text-muted d-block mt-2">Only CSV files are supported. The file should contain email, name, and status fields.</small>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="/customers/import/template" class="btn btn-outline-secondary">Download Template</a>
                                <button type="submit" class="btn btn-primary">Import Customers</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Import checklist</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="mdi mdi-check-circle-outline text-success me-2"></i>Use the official CSV template.</li>
                            <li class="mb-2"><i class="mdi mdi-check-circle-outline text-success me-2"></i>Confirm the file contains only valid email addresses.</li>
                            <li class="mb-2"><i class="mdi mdi-check-circle-outline text-success me-2"></i>Review import results in the console log if rows fail.</li>
                            <li><i class="mdi mdi-check-circle-outline text-success me-2"></i>The system validates entries before import.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function getToastContainer() {
    const containerId = 'globalToastContainer';
    let container = document.getElementById(containerId);
    if (!container) {
        container = document.createElement('div');
        container.id = containerId;
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1080';
        document.body.appendChild(container);
    }
    return container;
}

function showToast(message, type = 'success') {
    const container = getToastContainer();
    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-white border-0';
    toastEl.role = 'alert';
    toastEl.ariaLive = 'assertive';
    toastEl.ariaAtomic = 'true';

    const bgClass = type === 'error' ? 'bg-danger' : 'bg-success';
    const icon = type === 'error' ? '⚠️' : '✔️';

    toastEl.innerHTML = `
        <div class="d-flex ${bgClass} text-white rounded-3 shadow-sm">
            <div class="toast-body">
                <strong>${icon}</strong> ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    container.appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 4000 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

document.getElementById("importForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("/customers/import/process", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.created !== undefined) {
            showToast(`Import completed: ${data.created} created`, 'success');
        }

        if (data.failed && data.failed.length > 0) {
            showToast(`${data.failed.length} rows failed`, 'error');
            console.log("Failed rows:", data.failed);
        }

    })
    .catch(err => {
        console.error(err);
        showToast("Import failed", 'error');
    });
});
</script>