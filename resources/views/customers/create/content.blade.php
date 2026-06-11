<div class="main-panel" id="ajax-content">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="card-title mb-1">Add Customer</h4>
                                <p class="card-description text-muted mb-0">Create a Shopify customer with a validated business email.</p>
                            </div>
                            <span class="badge bg-soft-primary text-primary">Easy setup</span>
                        </div>

                        <form id="customerForm" class="forms-sample mt-4">
                            @csrf

                            <div class="row gx-3 gy-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" name="first_name" class="form-control" placeholder="Enter first name" autocomplete="given-name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" name="last_name" class="form-control" placeholder="Enter last name" autocomplete="family-name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="name@bounty.com.ph" autocomplete="email" required>
                                <small class="text-muted d-block mt-2">Allowed domains: @bounty.com.ph, @bvapcloud.com</small>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <button type="submit" id="submitBtn" class="btn btn-primary">Add Customer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card border-start border-4 border-primary">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Quick tips</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="mdi mdi-check-circle-outline text-success me-2"></i>Use a business email for Shopify validation.</li>
                            <li class="mb-2"><i class="mdi mdi-check-circle-outline text-success me-2"></i>Only one customer is created per submit.</li>
                            <li><i class="mdi mdi-check-circle-outline text-success me-2"></i>You can import bulk customers from the import page.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

