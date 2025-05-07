<!DOCTYPE html>
<html lang="en">
<head>
    <title>ColorWrap Client Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="ffassets/images/icons/cwiicon.png"/>
    <link rel="stylesheet" type="text/css" href="fassets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fassets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="fassets/vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="fassets/vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="fassets/vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="fassets/css/util.css">
    <link rel="stylesheet" type="text/css" href="fassets/css/main.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt>
                    <img src="fassets/images/newlogin.png" alt="IMG">
                </div>

                <form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <span class="login100-form-title">
                        ColorWrap Client Login
                    </span>

                    <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                        <input class="input100" type="text" name="email" placeholder="Email" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Password is required">
                        <input class="input100" type="password" name="password" placeholder="Password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>
                    
                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn" type="submit">
                            Login
                        </button>
                    </div>

                    <div class="text-center p-t-12">
                        <span class="txt1">Forgot</span>
                        <a class="txt2" href="#">Username / Password?</a>
                    </div>

                    <div class="text-center p-t-136">
                        <a class="txt2" href="#">
                            Contact CWI Customer Support
                            <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="fassets/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="fassets/vendor/bootstrap/js/popper.js"></script>
    <script src="fassets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="fassets/vendor/select2/select2.min.js"></script>
    <script src="fassets/vendor/tilt/tilt.jquery.min.js"></script>
    <script>
        $('.js-tilt').tilt({ scale: 1.1 });

        // Show SweetAlert success message if authenticated
        @if (session('login_success'))
            Swal.fire({
                icon: 'success',
                title: 'Login Successful',
                text: 'Welcome to your dashboard!',
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
            });
        @endif
    </script>
    <script src="fassets/js/main.js"></script>
</body>
</html>
