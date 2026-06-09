<div class="main-panel" id="ajax-content">
    <div class="content-wrapper">

        <h4>Export History</h4>

        <x-log-table
            api="/api/logs/export-history"
            :columns="['id','user_id','export_type','status','completed_at']"
        />

    </div>
</div>