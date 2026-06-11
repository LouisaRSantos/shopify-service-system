<div class="main-panel" id="ajax-content">
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-md-8">
                <h4 class="mb-1">Configuration Settings</h4>
                <p class="text-muted mb-0">
                    Control export cleanup retention and dashboard cache settings.
                    Values are loaded immediately, then updated in the background.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="configStatus" class="alert alert-info" role="status">
                    Loading current settings...
                </div>

                <form id="configForm">
                    <div class="row gy-3">
                        <div class="col-12">
                            <h6 class="mb-3">Export Cleanup</h6>
                        </div>

                        <div class="col-md-6">
                            <label for="export_retention_days" class="form-label">Retention Days</label>
                            <input type="number"
                                   class="form-control"
                                   name="export_retention_days"
                                   id="export_retention_days"
                                   min="0"
                                   placeholder="e.g. 30">
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="col-12">
                            <h6 class="mb-3">Dashboard Cache Settings</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="dashboard_customer_count_cache_minutes" class="form-label">Customers Count Cache</label>
                            <input type="number"
                                   class="form-control"
                                   name="dashboard_customer_count_cache_minutes"
                                   id="dashboard_customer_count_cache_minutes"
                                   min="0"
                                   placeholder="minutes">
                        </div>

                        <div class="col-md-4">
                            <label for="dashboard_invited_count_cache_minutes" class="form-label">Invited Count Cache</label>
                            <input type="number"
                                   class="form-control"
                                   name="dashboard_invited_count_cache_minutes"
                                   id="dashboard_invited_count_cache_minutes"
                                   min="0"
                                   placeholder="minutes">
                        </div>

                        <div class="col-md-4">
                            <label for="dashboard_enabled_count_cache_minutes" class="form-label">Enabled Count Cache</label>
                            <input type="number"
                                   class="form-control"
                                   name="dashboard_enabled_count_cache_minutes"
                                   id="dashboard_enabled_count_cache_minutes"
                                   min="0"
                                   placeholder="minutes">
                        </div>

                        <div class="col-12">
                            <button type="button" id="configSaveBtn" class="btn btn-primary">
                                Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    window.initConfigPage = function () {
        var $status = $('#configStatus');
        var $saveBtn = $('#configSaveBtn');

        $status
            .removeClass('alert-danger alert-success')
            .addClass('alert-info')
            .text('Loading current settings...')
            .show();

        $saveBtn.prop('disabled', true);

        $.ajax({
            url: '/api/configuration',
            method: 'GET',
            dataType: 'json'
        })
        .done(function (res) {
            if (!res.settings || !res.settings.length) {
                $status
                    .removeClass('alert-info')
                    .addClass('alert-warning')
                    .text('No configuration values found.');
                return;
            }

            res.settings.forEach(function (item) {
                var field = $('#' + item.setting_key);
                if (field.length) {
                    field.val(item.setting_value);
                }
            });

            $status
                .removeClass('alert-info')
                .addClass('alert-success')
                .text('Current settings loaded. You can edit and save below.');
        })
        .fail(function () {
            $status
                .removeClass('alert-info')
                .addClass('alert-danger')
                .text('Unable to load settings. Please try again.');
            if (typeof showToast === 'function') {
                showToast('Failed to load configuration values.', 'error');
            }
        })
        .always(function () {
            $saveBtn.prop('disabled', false);
        });
    };

    function submitConfigForm() {
        var $saveBtn = $('#configSaveBtn');
        var formData = $('#configForm').serializeArray();
        var token = $('meta[name="csrf-token"]').attr('content');

        $saveBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: '/configuration/update',
            method: 'POST',
            dataType: 'json',
            data: {
                settings: formData,
                _token: token
            }
        })
        .done(function (res) {
            if (res.status === 'success') {
                if (typeof showToast === 'function') {
                    showToast(res.message || 'Settings saved successfully.', 'success');
                }
                $('#configStatus')
                    .removeClass('alert-info alert-danger')
                    .addClass('alert-success')
                    .text('Settings saved successfully.');
            } else {
                if (typeof showToast === 'function') {
                    showToast(res.message || 'Failed to save settings.', 'error');
                }
                $('#configStatus')
                    .removeClass('alert-info alert-success')
                    .addClass('alert-danger')
                    .text(res.message || 'Failed to save settings.');
            }
        })
        .fail(function (xhr) {
            var message = 'Server error while saving settings.';
            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            if (typeof showToast === 'function') {
                showToast(message, 'error');
            }
            $('#configStatus')
                .removeClass('alert-info alert-success')
                .addClass('alert-danger')
                .text(message);
        })
        .always(function () {
            $saveBtn.prop('disabled', false).text('Save Settings');
        });
    }

    $(document).off('click.configSave', '#configSaveBtn');
    $(document).on('click.configSave', '#configSaveBtn', function (e) {
        e.preventDefault();
        submitConfigForm();
    });

    initConfigPage();
</script>