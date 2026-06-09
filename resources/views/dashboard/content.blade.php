<div class="main-panel">
    <div class="content-wrapper">
        <style>
            .dashboard-card { border-radius: 18px; transition: transform .25s ease, box-shadow .25s ease; }
            .dashboard-card:hover { transform: translateY(-4px); box-shadow: 0 18px 35px rgba(15, 23, 42, 0.12); }
            .dashboard-card-icon { width: 56px; height: 56px; display: inline-flex; align-items: center; justify-content: center; border-radius: 16px; font-size: 1.4rem; }
            .dashboard-skeleton .placeholder { min-height: 20px; border-radius: 12px; }
            .dashboard-skeleton .placeholder.col-6 { max-width: 50%; }
            @media (max-width: 768px) {
                .dashboard-card { text-align: center; }
                .dashboard-card .card-body { flex-direction: column; align-items: flex-start; }
                .dashboard-card-icon { margin: 0 auto 16px; }
            }
        </style>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-dark text-white overflow-hidden" style="border-radius:18px;">
                    <div class="card-body p-4 p-md-5 d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                        <div>
                            <p class="text-uppercase text-secondary mb-2">Dashboard overview</p>
                            <h2 class="fw-bold mb-2">Shopify customer insights</h2>
                            <p class="mb-0 text-white-75">Live counts from Shopify and the latest customers added within the last seven days.</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-warning text-dark">Auto-refresh on load</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4" id="dashboard-summary-cards">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card dashboard-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="dashboard-card-icon bg-primary text-white"><i class="mdi mdi-account-multiple"></i></div>
                        <div>
                            <p class="text-uppercase text-muted mb-1 small">Total customers</p>
                            <h3 id="dashboard-total-customers" class="mb-1">—</h3>
                            <p class="text-muted small mb-0">All Shopify customers count.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card dashboard-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="dashboard-card-icon bg-success text-white"><i class="mdi mdi-account-check"></i></div>
                        <div>
                            <p class="text-uppercase text-muted mb-1 small">Total invited</p>
                            <h3 id="dashboard-total-invited" class="mb-1">—</h3>
                            <p class="text-muted small mb-0">Customers invited through Shopify state filter.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card dashboard-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="dashboard-card-icon bg-warning text-white"><i class="mdi mdi-account-star"></i></div>
                        <div>
                            <p class="text-uppercase text-muted mb-1 small">Total enabled</p>
                            <h3 id="dashboard-total-enabled" class="mb-1">—</h3>
                            <p class="text-muted small mb-0">Customers with enabled state in Shopify.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
                            <div>
                                <h5 class="mb-2">Recently added customers</h5>
                                <p class="text-muted mb-0">Showing customers added in the last week.</p>
                            </div>
                            <button id="dashboard-refresh-button" class="btn btn-outline-primary btn-sm">Refresh data</button>
                        </div>

                        <div id="dashboard-card-skeleton" class="dashboard-skeleton mb-3">
                            <div class="placeholder-glow">
                                <div class="placeholder col-6 mb-3"></div>
                                <div class="placeholder col-12 mb-2" style="height:18px"></div>
                                <div class="placeholder col-12 mb-2" style="height:18px"></div>
                                <div class="placeholder col-12 mb-2" style="height:18px"></div>
                            </div>
                        </div>

                        <div id="dashboard-load-error" class="alert alert-danger d-none" role="alert">
                            Unable to load dashboard data. Please refresh or try again later.
                        </div>

                        <div id="dashboard-table-wrapper" class="table-responsive" style="display:none;">
                            <table class="table table-borderless table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-uppercase text-muted small">
                                        <th class="ps-0">Name</th>
                                        <th>Email</th>
                                        <th>State</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody id="dashboard-recent-customers-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')
</div>

@push('scripts')
<script>
    function initDashboard() {
        const summaryUrl = "{{ url('/api/dashboard/summary') }}";
        const $customersBody = $('#dashboard-recent-customers-body');
        const $tableWrapper = $('#dashboard-table-wrapper');
        const $skeleton = $('#dashboard-card-skeleton');
        const $error = $('#dashboard-load-error');

        function renderCounts(counts) {
            $('#dashboard-total-customers').text(counts.customers.toLocaleString());
            $('#dashboard-total-invited').text(counts.invited.toLocaleString());
            $('#dashboard-total-enabled').text(counts.enabled.toLocaleString());
        }

        function renderRecent(customers) {
            $customersBody.empty();

            if (!customers.length) {
                $customersBody.append('<tr><td colspan="4" class="text-muted">No recently added customers found.</td></tr>');
                return;
            }

            customers.forEach(function (customer) {
                const created = customer.created_at
                    ? new Date(customer.created_at).toLocaleDateString()
                    : '-';

                const name = ((customer.first_name || '') + ' ' + (customer.last_name || '')).trim();

                $customersBody.append(`
                    <tr>
                        <td class="ps-0"><strong>${name || 'No name'}</strong></td>
                        <td>${customer.email || '-'}</td>
                        <td>${customer.state || '-'}</td>
                        <td>${created}</td>
                    </tr>
                `);
            });
        }

        function showLoading() {
            $error.addClass('d-none');
            $tableWrapper.hide();
            $skeleton.show();
        }

        function showResults() {
            $skeleton.hide();
            $tableWrapper.show();
            $error.addClass('d-none');
        }

        function showError() {
            $skeleton.hide();
            $tableWrapper.hide();
            $error.removeClass('d-none');
        }

        function loadDashboard() {
            showLoading();

            $.ajax({
                url: summaryUrl,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    renderCounts(response.counts || {});
                    renderRecent(response.recent_customers || []);
                    showResults();
                },
                error: function () {
                    showError();
                }
            });
        }

        // expose reload so other pages can trigger it
        window.__loadDashboard = loadDashboard;

        loadDashboard();
    }
    window.initDashboard = initDashboard;

    function bootDashboard() {
        if (typeof window.initDashboard === 'function') {
            window.initDashboard();
        }
    }

    $(function () {
        bootDashboard();
    });
</script>
@endpush
