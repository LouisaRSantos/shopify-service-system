<div class="main-panel" id="ajax-content">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-xl-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h4 class="card-title mb-1">Export Customers</h4>
                                <p class="card-description text-muted mb-0">Export Shopify customers using filters and selected columns.</p>
                            </div>
                            <span class="badge bg-soft-warning text-warning">Export tool</span>
                        </div>

                        <div class="row gx-3 gy-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Export Type</label>
                                    <select id="exportType" class="form-control">
                                        <option value="all">All Customers</option>
                                        <option value="email">By Email</option>
                                        <option value="ids">By IDs</option>
                                        <option value="state">By State</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 d-none" id="emailField">
                                    <label class="form-label">Email</label>
                                    <input type="email" id="exportEmail" class="form-control" placeholder="customer@example.com">
                                </div>
                                <div class="mb-3 d-none" id="idsField">
                                    <label class="form-label">Customer IDs (comma separated)</label>
                                    <input type="text" id="exportIds" class="form-control" placeholder="123, 456, 789">
                                </div>
                                <div class="mb-3 d-none" id="stateField">
                                    <label class="form-label">State</label>
                                    <select id="exportState" class="form-control">
                                        <option value="enabled">Enabled</option>
                                        <option value="invited">Invited</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start mb-3">
                            <div>
                                <label class="form-label mb-1">Select Columns</label>
                                <p class="text-muted small mb-0">Pick the fields you want included in your export.</p>
                            </div>
                            <button id="toggleColumnsBtn" type="button" class="btn btn-sm btn-outline-primary mt-2 mt-sm-0">Select All</button>
                        </div>

                        <div class="mb-3">
                            <div class="row g-3">
                                @php
                                    $columns = [
                                        'id','email','first_name','last_name','phone',
                                        'state','orders_count','total_spent',
                                        'created_at','updated_at'
                                    ];
                                @endphp

                                @foreach($columns as $col)
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input export-column" value="{{ $col }}">
                                            <label class="form-check-label">
                                                {{ ucfirst(str_replace('_',' ', $col)) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button id="exportBtn" class="btn btn-primary mt-3 w-100">Start Export</button>
                        <div id="exportResult" class="mt-3"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 grid-margin stretch-card">
                <div class="card border-start border-warning border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Export tips</h6>
                        <p class="text-muted">Choose the best filter for your export and select only the columns you need to reduce file size.</p>
                        <div class="mb-3">
                            <strong>All Customers</strong>
                            <p class="text-muted mb-0">Downloads the full customer list.</p>
                        </div>
                        <div class="mb-3">
                            <strong>By Email</strong>
                            <p class="text-muted mb-0">Export one customer record by email.</p>
                        </div>
                        <div class="mb-0">
                            <strong>By IDs</strong>
                            <p class="text-muted mb-0">Separate multiple IDs with commas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
