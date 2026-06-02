<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="{{ asset('assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-4 mx-auto">

                    <div class="auth-form-light text-left py-5 px-4 px-sm-5">

                        <h4>Hello! let's get started</h4>
                        <h6 class="font-weight-light">Sign in to continue.</h6>

                        <form class="pt-3" method="POST" action="{{ route('login.submit') }}">
                            @csrf

                            <div class="form-group">
                                <input type="text"
                                       name="username"
                                       class="form-control form-control-lg"
                                       placeholder="Username"
                                       required>
                            </div>

                            <div class="form-group">
                                <input type="password"
                                       name="password"
                                       class="form-control form-control-lg"
                                       placeholder="Password"
                                       required>
                            </div>

                            <div class="mt-3 d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    SIGN IN
                                </button>
                            </div>

                            @if(session('error'))
                                <div class="text-danger mt-3">
                                    {{ session('error') }}
                                </div>
                            @endif

                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>