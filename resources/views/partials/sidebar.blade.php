@php
    $isAdmin = session('web_user_type') === 'admin';
@endphp

<nav class="sidebar sidebar-offcanvas" id="sidebar">

    <ul class="nav">

        <li class="nav-item">
            <a class="nav-link ajax-link" href="/">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <li class="nav-item nav-category">
            Bounty Family
        </li>

        <li class="nav-item">
            <a class="nav-link ajax-link" href="/customers/create">
                <i class="icon-square-plus menu-icon"></i>
                <span class="menu-title">Add Customer</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link ajax-link" href="/customers/import">
                <i class="icon-cloud-upload menu-icon"></i>
                <span class="menu-title">Import Customers</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link ajax-link" href="/customers/export">
                <i class="icon-cloud-download menu-icon"></i>
                <span class="menu-title">Export Customers</span>
            </a>
        </li>

        @if($isAdmin)
            <li class="nav-item nav-category">
                Bounty Fresh Market
            </li>

            <li class="nav-item">
                <a class="nav-link ajax-link" href="/">
                    <i class="icon-clock menu-icon"></i>
                    <span class="menu-title">Marketing Scheduler</span>
                </a>
            </li>

            <li class="nav-item nav-category">
                Administration Logs
            </li>

            <li class="nav-item">
                <a class="nav-link ajax-link" href="/logs/customer-activity">
                    <i class="icon-marquee menu-icon"></i>
                    <span class="menu-title">Customer Activity</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link ajax-link" href="/logs/export-history">
                    <i class="icon-download menu-icon"></i>
                    <span class="menu-title">Export History</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link ajax-link" href="/logs/system-logs">
                    <i class="icon-archive menu-icon"></i>
                    <span class="menu-title">System Logs</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link ajax-link" href="/logs/api-usage">
                    <i class="icon-server menu-icon"></i>
                    <span class="menu-title">API Usage Logs</span>
                </a>
            </li>

            <li class="nav-item nav-category">
                System Settings
            </li>

            <li class="nav-item">
                <a class="nav-link ajax-link" href="/configuration">
                    <i class="icon-monitor menu-icon"></i>
                    <span class="menu-title">Configuration</span>
                </a>
            </li>
        @endif

    </ul>

</nav>