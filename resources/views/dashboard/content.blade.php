<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start">
                        <div>
                            <h3 class="font-weight-bold mb-2">Welcome to your dashboard</h3>
                            <p class="mb-0">
                                Manage Shopify customers from one place. Add a new customer, import bulk records, or export selected customer data.
                            </p>
                        </div>
                        <!-- <div class="mt-3 mt-md-0">
                            <a href="/customers/create" class="btn btn-light btn-sm me-2">Add Customer</a>
                            <a href="/customers/import" class="btn btn-outline-light btn-sm me-2">Import</a>
                            <a href="/customers/export" class="btn btn-outline-light btn-sm">Export</a>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-account-multiple-outline mdi-32px text-primary me-3"></i>
                            <div>
                                <h6 class="text-muted mb-1">Customer Management</h6>
                                <h4 class="font-weight-bold mb-0">Create & update records</h4>
                            </div>
                        </div>
                        <p class="mt-3 text-muted">
                            Add new Shopify customers with validated details and keep your customer list updated.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-file-import mdi-32px text-success me-3"></i>
                            <div>
                                <h6 class="text-muted mb-1">Bulk Import</h6>
                                <h4 class="font-weight-bold mb-0">CSV uploads</h4>
                            </div>
                        </div>
                        <p class="mt-3 text-muted">
                            Import customers quickly using the provided template. The system validates rows and reports failures.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-file-export mdi-32px text-warning me-3"></i>
                            <div>
                                <h6 class="text-muted mb-1">Export Data</h6>
                                <h4 class="font-weight-bold mb-0">Filtered spreadsheets</h4>
                            </div>
                        </div>
                        <p class="mt-3 text-muted">
                            Export customers using filters and column selection so you only download the data you need.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')
</div>
