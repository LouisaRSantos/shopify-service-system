<div class="main-panel" id="ajax-content">
    <div class="content-wrapper">

        <h4>System Logs</h4>

        <x-log-table
            api="/api/logs/system-logs"
            :columns="['id','type','command','status','started_at','finished_at']"
        />

    </div>
</div>