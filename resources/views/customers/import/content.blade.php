<div class="main-panel" id="ajax-content">

    <div class="content-wrapper">

        <div class="row">

            <div class="col-md-12 grid-margin">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title">
                            Import Customers
                        </h4>

                        <p class="card-description">
                            Upload a CSV file to import customers into Shopify
                        </p>

                        <form id="importForm" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>CSV File</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>

                            <div class="mt-3">
                                <a href="/customers/import/template" class="btn btn-light">
                                    Download Template
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    Import Customers
                                </button>
                            </div>

                        </form>

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