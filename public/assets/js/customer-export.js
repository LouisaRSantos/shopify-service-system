function initializeCustomerExport() {

    const $exportType = $('#exportType');
    const $exportBtn = $('#exportBtn');
    const $exportResult = $('#exportResult');
    const $toggleColumnsBtn = $('#toggleColumnsBtn');
    const $columnCheckboxes = $('.export-column');

    if (
        !$exportType.length ||
        !$exportBtn.length ||
        !$exportResult.length ||
        !$toggleColumnsBtn.length ||
        !$columnCheckboxes.length
    ) {
        return;
    }

    if ($exportBtn.data('exportInitialized') === true) {
        return;
    }

    $exportBtn.data('exportInitialized', true);

    const $emailField = $('#emailField');
    const $idsField = $('#idsField');
    const $stateField = $('#stateField');

    let pollInterval = null;
    let isPolling = false;

    function updateToggleButton() {
        const total = $columnCheckboxes.length;
        const selected = $columnCheckboxes.filter(':checked').length;
        if (selected === total) {
            $toggleColumnsBtn.text('Unselect All');
        } else {
            $toggleColumnsBtn.text('Select All');
        }
    }

    function setAllColumns(checked) {
        $columnCheckboxes.prop('checked', checked);
        updateToggleButton();
    }

    $toggleColumnsBtn.on('click', function () {
        const allSelected = $columnCheckboxes.length === $columnCheckboxes.filter(':checked').length;
        setAllColumns(!allSelected);
    });

    $columnCheckboxes.on('change', updateToggleButton);

    setAllColumns(false);

    function clearPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    window.__customerExportCleanup = clearPolling;

    $exportType.on('change', function () {
        $emailField.addClass('d-none');
        $idsField.addClass('d-none');
        $stateField.addClass('d-none');

        if ($(this).val() === 'email') {
            $emailField.removeClass('d-none');
        }

        if ($(this).val() === 'ids') {
            $idsField.removeClass('d-none');
        }

        if ($(this).val() === 'state') {
            $stateField.removeClass('d-none');
        }
    });

    function pollStatus() {
        if (isPolling) {
            return;
        }
        isPolling = true;
        $.ajax({
            url: '/customers/export/status',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (data) {
                console.log('EXPORT STATUS:', data);
                if (data.status === 'COMPLETED') {
                    clearPolling();
                    $exportBtn.prop('disabled', false);
                    $exportResult.html(`
                        <div class="alert alert-success">
                            Export ready!
                            <a href="${data.download}" class="btn btn-sm btn-primary mt-2">
                                Download Excel
                            </a>
                        </div>
                    `);
                    return;
                }

                if (
                    data.status === 'FAILED' ||
                    data.status === 'CANCELED'
                ) {
                    clearPolling();
                    $exportBtn.prop('disabled', false);
                    $exportResult.html(`
                        <div class="alert alert-danger">
                            Export failed or canceled.
                        </div>
                    `);
                    return;
                }
            },

            error: function (xhr, status, error) {
                console.error(error);
            },

            complete: function () {
                isPolling = false;
            }
        });
    }

    function startPolling() {
        if (pollInterval) {
            return;
        }
        pollInterval = setInterval(pollStatus, 3000);
        pollStatus();
    }

    $exportBtn.on('click', function (event) {
        event.preventDefault();
        if ($exportBtn.prop('disabled')) {
            return;
        }
        const type = $exportType.val();
        const columns = $('.export-column:checked')
            .map(function () {
                return $(this).val();
            })
            .get();

        if (columns.length === 0) {
            $exportResult.html(
                `<div class="alert alert-danger">Please select at least one column before exporting.</div>`
            );
            $exportBtn.prop('disabled', false);
            return;
        }

        let payload = {
            type: type,
            columns: columns
        };

        if (type === 'email') {
            const email = $.trim($('#exportEmail').val());
            if (!email) {
                $exportResult.html(
                    `<div class="alert alert-danger">Email is required</div>`
                );
                return;
            }
            payload.email = email;
        }

        if (type === 'ids') {
            payload.ids = $('#exportIds')
                .val()
                .split(',')
                .map(id => id.trim())
                .filter(Boolean)
                .join(',');
        }

        if (type === 'state') {
            payload.state = $('#exportState').val() || '';
        }

        $exportBtn.prop('disabled', true);
        $exportResult.html(
            `<div class="alert alert-info">Starting export...</div>`
        );

        $.ajax({
            url: '/customers/export/start',
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN':
                    $('meta[name="csrf-token"]').attr('content') || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: JSON.stringify(payload),
            success: function (data) {
                if (data.status !== 'success') {
                    clearPolling();
                    $exportBtn.prop('disabled', false);
                    $exportResult.html(`
                        <div class="alert alert-danger">
                            ${data.message || 'Export failed to start.'}
                        </div>
                    `);
                    return;
                }
                startPolling();
            },
            error: function () {
                clearPolling();
                $exportBtn.prop('disabled', false);
                $exportResult.html(`
                    <div class="alert alert-danger">
                        Network error starting export.
                    </div>
                `);
            }
        });
    });
    $(window).on('beforeunload', clearPolling);
}

$(function () {
    if ($('#exportType').length) {
        initializeCustomerExport();
    }
});