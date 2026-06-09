<div class="main-panel" id="ajax-content">
    <div class="content-wrapper">

        <h4>Customer Activity Logs</h4>

        <x-log-table
            api="/api/logs/customer-activity"
            :columns="['id','user_id','activity_type','status','completed_at']"
        />

    </div>
</div>