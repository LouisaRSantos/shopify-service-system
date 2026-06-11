<div class="main-panel" id="ajax-content">
    <div class="content-wrapper">

        <h4>API Usage Logs</h4>

        <x-log-table
            api="/api/logs/api-usage"
            :columns="['id','user_id','method','endpoint','action','response_status','created_at']"
        />

    </div>
</div>