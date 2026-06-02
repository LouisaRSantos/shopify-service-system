function initializeCustomerExport() {
    const exportType = document.getElementById('exportType');
    const exportBtn = document.getElementById('exportBtn');
    const exportResult = document.getElementById('exportResult');

    if (!exportType || !exportBtn || !exportResult) {
        return;
    }

    if (exportBtn.dataset.exportInitialized === 'true') {
        return;
    }

    exportBtn.dataset.exportInitialized = 'true';

    const emailField = document.getElementById('emailField');
    const idsField = document.getElementById('idsField');
    const stateField = document.getElementById('stateField');

    let pollInterval = null;
    let isPolling = false;

    function clearPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    window.__customerExportCleanup = clearPolling;

    exportType.addEventListener('change', function () {
        emailField?.classList.add('d-none');
        idsField?.classList.add('d-none');
        stateField?.classList.add('d-none');

        if (this.value === 'email') {
            emailField?.classList.remove('d-none');
        }

        if (this.value === 'ids') {
            idsField?.classList.remove('d-none');
        }

        if (this.value === 'state') {
            stateField?.classList.remove('d-none');
        }
    });

    async function pollStatus() {
        if (isPolling) {
            return;
        }

        isPolling = true;

        try {
            const res = await fetch('/customers/export/status', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!res.ok) {
                throw new Error('Status request failed');
            }

            const data = await res.json();

            console.log('EXPORT STATUS:', data);

            if (data.status === 'COMPLETED') {
                clearPolling();
                exportBtn.disabled = false;
                exportResult.innerHTML = `
                    <div class="alert alert-success">
                        Export ready!
                        <a href="${data.download}" class="btn btn-sm btn-primary mt-2">
                            Download Excel
                        </a>
                    </div>`;
                return;
            }

            if (data.status === 'FAILED' || data.status === 'CANCELED') {
                clearPolling();
                exportBtn.disabled = false;
                exportResult.innerHTML = `
                    <div class="alert alert-danger">
                        Export failed or canceled.
                    </div>`;
                return;
            }
        } catch (error) {
            console.error(error);
        } finally {
            isPolling = false;
        }
    }

    function startPolling() {
        if (pollInterval) {
            return;
        }

        pollInterval = setInterval(pollStatus, 3000);
        pollStatus();
    }

    exportBtn.addEventListener('click', async function (event) {
        event.preventDefault();

        if (exportBtn.disabled) {
            return;
        }

        const type = exportType.value;
        const columns = Array.from(document.querySelectorAll('.export-column:checked')).map(el => el.value);

        let payload = {
            type: type,
            columns: columns,
        };

        if (type === 'email') {
            const email = document.getElementById('exportEmail').value.trim();

            if (!email) {
                document.getElementById('exportResult').innerHTML =
                    `<div class="alert alert-danger">Email is required</div>`;
                return;
            }

            payload.email = email;
        }

        if (type === 'ids') {
            payload.ids = document.getElementById('exportIds').value
                .split(',')
                .map(id => id.trim())
                .filter(Boolean)
                .join(',');
        }

        if (type === 'state') {
            payload.state = document.getElementById('exportState')?.value || '';
        }

        exportBtn.disabled = true;
        exportResult.innerHTML = `<div class="alert alert-info">Starting export...</div>`;

        try {
            const res = await fetch('/customers/export/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (data.status !== 'success') {
                clearPolling();
                exportBtn.disabled = false;
                exportResult.innerHTML = `<div class="alert alert-danger">${data.message || 'Export failed to start.'}</div>`;
                return;
            }

            startPolling();
        } catch (error) {
            console.error(error);
            clearPolling();
            exportBtn.disabled = false;
            exportResult.innerHTML = `<div class="alert alert-danger">Network error starting export.</div>`;
        }
    });

    window.addEventListener('beforeunload', clearPolling);
}

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('exportType')) {
        initializeCustomerExport();
    }
});