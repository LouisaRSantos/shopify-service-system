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

