<div class="main-panel" id="ajax-content">

    <div class="content-wrapper">

        <div class="row">
            <div class="col-md-12 grid-margin">

                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Add Customer</h4>
                        <p class="card-description">
                            Create Shopify customer with validated business email
                        </p>

                        <form id="customerForm" class="forms-sample mt-4">

                            @csrf

                            <div class="form-group mb-3">
                                <label>First Name</label>
                                <input type="text"
                                       name="first_name"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Last Name</label>
                                <input type="text"
                                       name="last_name"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Email Address</label>
                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       required>
                            </div>

                            <button type="submit"
                                    id="submitBtn"
                                    class="btn btn-primary me-2">
                                Add Customer
                            </button>

                            <small class="text-muted d-block mt-2">
                                Allowed domains: @bounty.com.ph, @bvapcloud.com
                            </small>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

<script>
const form = document.getElementById("customerForm");
const btn = document.getElementById("submitBtn");

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

function showToast(message, type = "success") {
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

form.addEventListener("submit", async function (e) {
    e.preventDefault();

    btn.disabled = true;
    btn.innerText = "Processing...";

    const formData = new FormData(form);

    try {
        const res = await fetch("/customers/store", {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        });

        const data = await res.json();

        showToast(data.message, data.status);

        if (data.status === "success") {
            form.reset();
        }

    } catch (err) {
        showToast("Network error", "error");
    }

    btn.disabled = false;
    btn.innerText = "Add Customer";
});
</script>