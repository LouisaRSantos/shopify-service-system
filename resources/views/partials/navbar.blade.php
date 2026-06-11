<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-logo me-5 ajax-link" href="/">
            <img src="{{ asset('assets/images/logo/bounty-services-logo-1.png') }}" class="me-2" alt="logo" />
        </a>

        <a class="navbar-brand brand-logo-mini ajax-link" href="/">
            <img src="{{ asset('assets/images/logo/bounty-services-logo-circle.png') }}" alt="logo" />
        </a>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

        <button class="navbar-toggler navbar-toggler align-self-center"
                type="button"
                data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>

        <!-- <ul class="navbar-nav mr-lg-2">
            <li class="nav-item nav-search d-none d-lg-block">
                <div class="input-group">

                    <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                        <span class="input-group-text" id="search">
                            <i class="icon-search"></i>
                        </span>
                    </div>

                    <input type="text"
                           class="form-control"
                           id="navbar-search-input"
                           placeholder="Search now">

                </div>
            </li>
        </ul> -->

        <ul class="navbar-nav navbar-nav-right">

            <!-- <li class="nav-item dropdown">

                <a class="nav-link count-indicator dropdown-toggle"
                   id="notificationDropdown"
                   href="#"
                   data-bs-toggle="dropdown">

                    <i class="icon-bell mx-0"></i>
                    <span class="count"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list">

                    <p class="mb-0 font-weight-normal float-left dropdown-header">
                        Notifications
                    </p>

                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-success">
                                <i class="ti-info-alt mx-0"></i>
                            </div>
                        </div>

                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">
                                Application Error
                            </h6>

                            <p class="font-weight-light small-text mb-0 text-muted">
                                Just now
                            </p>
                        </div>
                    </a>

                </div>
            </li> -->

            <li class="nav-item nav-profile dropdown">

                <a class="nav-link dropdown-toggle d-flex align-items-center"
                    href="#"
                    data-bs-toggle="dropdown">

                        <img src="{{ asset('assets/images/faces/circle-user.png') }}"
                            alt="profile" />

                        <span class="ms-2 d-none d-lg-inline">
                            {{ session('web_full_name') }}
                        </span>

                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown">
                    <div class="dropdown-item-text">
                        <strong>{{ session('web_full_name') }}</strong><br>
                        <small>{{ session('web_username') }}</small>
                    </div>

                    <div class="dropdown-divider"></div>

                    <!-- <a class="dropdown-item">
                        <i class="ti-settings text-primary"></i>
                        Settings
                    </a> -->

                    <a class="dropdown-item" href="{{ route('logout') }}">
                        <i class="ti-power-off text-primary"></i> Logout
                    </a>

                </div>
            </li>

            <!-- <li class="nav-item nav-settings d-none d-lg-flex">
                <a class="nav-link" href="#">
                    <i class="icon-ellipsis"></i>
                </a>
            </li> -->

        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center"
                type="button"
                data-toggle="offcanvas">

            <span class="icon-menu"></span>

        </button>

    </div>

</nav>