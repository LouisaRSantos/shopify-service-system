@props([
    'api' => '',
    'columns' => []
])

@php $logId = 'log_' . uniqid(); @endphp

<div class="log-module" data-log-id="{{ $logId }}">
    <style>
        .log-module { background:#fff; padding:16px; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04); }
        .log-toolbar { display:flex; gap:8px; align-items:center; margin-bottom:12px; flex-wrap:wrap; }
        .log-filter-panel { display:none; gap:8px; align-items:center; margin-bottom:12px; }
        .log-filter-panel input { min-width:120px; }
        .log-table-responsive { overflow:auto; }
        .log-skeleton .placeholder { height:14px; background:linear-gradient(90deg,#f0f0f0,#e8e8e8); border-radius:4px; }
        @media (max-width:600px) {
            .log-filter-panel { flex-direction:column; align-items:stretch; }
            .log-toolbar { justify-content:space-between; }
        }
    </style>

    <div class="log-toolbar">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-outline-primary log-toggle-filter">Filter</button>
            <button type="button" class="btn btn-sm btn-outline-secondary ms-2 log-clear-filters">Clear</button>
        </div>
        <div class="ms-auto text-muted small">Showing recent results</div>
    </div>

    <div class="log-filter-panel">
        @foreach($columns as $col)
            <div class="form-group">
                <label class="small mb-1">{{ ucfirst(str_replace('_',' ', $col)) }}</label>
                <input type="text" class="form-control form-control-sm log-filter-input" data-filter-key="{{ $col }}" placeholder="Filter {{ $col }}">
            </div>
        @endforeach
        <div class="d-flex align-items-end">
            <button type="button" class="btn btn-sm btn-primary log-apply-filters ms-2">Apply</button>
        </div>
    </div>

    <!-- Skeleton -->
    <div class="log-skeleton">
        <div class="placeholder-glow">
            <div class="placeholder col-12 mb-2"></div>
            <div class="placeholder col-12 mb-2"></div>
            <div class="placeholder col-12 mb-2"></div>
        </div>
    </div>

    <!-- Table -->
    <div class="log-table-wrapper" style="display:none;">
        <div class="log-table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        @foreach($columns as $col)
                            <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody></tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="log-pagination mt-3"></div>
    </div>

</div>

<script>
window.__logTableConfig = window.__logTableConfig || [];

window.__logTableConfig.push({
    el: document.querySelector('[data-log-id="{{ $logId }}"]'),
    api: @json($api),
    columns: @json($columns)
});
</script>