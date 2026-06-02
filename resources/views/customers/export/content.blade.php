<div class="main-panel" id="ajax-content">

    <div class="content-wrapper">

        <div class="row">

            <div class="col-md-12 grid-margin">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title">Export Customers</h4>
                        <p class="card-description">
                            Export Shopify customers using filters and column selection
                        </p>

                        {{-- FILTER TYPE --}}
                        <div class="mb-3">
                            <label>Export Type</label>
                            <select id="exportType" class="form-control">
                                <option value="all">All Customers</option>
                                <option value="email">By Email</option>
                                <option value="ids">By IDs</option>
                                <option value="state">By State</option>
                            </select>
                        </div>

                        {{-- EMAIL --}}
                        <div class="mb-3 d-none" id="emailField">
                            <label>Email</label>
                            <input type="email" id="exportEmail" class="form-control">
                        </div>

                        {{-- IDS --}}
                        <div class="mb-3 d-none" id="idsField">
                            <label>Customer IDs (comma separated)</label>
                            <input type="text" id="exportIds" class="form-control">
                        </div>

                        {{-- STATE --}}
                        <div class="mb-3 d-none" id="stateField">
                            <label>State</label>
                            <select id="exportState" class="form-control">
                                <option value="enabled">Enabled</option>
                                <option value="invited">Invited</option>
                            </select>
                        </div>

                        <hr>

                        {{-- COLUMNS --}}
                        <label>Select Columns</label>
                        <div class="row">

                            @php
                                $columns = [
                                    'id','email','first_name','last_name','phone',
                                    'state','orders_count','total_spent',
                                    'created_at','updated_at'
                                ];
                            @endphp

                            @foreach($columns as $col)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input export-column"
                                               value="{{ $col }}"
                                               checked>
                                        <label class="form-check-label">
                                            {{ ucfirst(str_replace('_',' ', $col)) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        <button id="exportBtn" class="btn btn-primary mt-4 w-100">
                            Start Export
                        </button>

                        <div id="exportResult" class="mt-3"></div>

                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
