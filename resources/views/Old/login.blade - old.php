<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickBooks Clients Order</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="form-container">
        <div class="login-container" id="login-container">
            <h1 class="title">Log In</h1>
            <p class="desc">Login to your account to place an order with ColorWrap</p>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-container">
                    <input type="email" id="email" name="email" placeholder="Enter Your Email Address" autofocus>
                </div>
                <div class="input-container">
                    <input type="password" id="password" name="password" placeholder="Enter Your Password">
                    <button type="button" id="toggle-password"><i class="fas fa-eye" id="toggle-icon"></i></button>
                </div>
                <span class="line"></span><br><br>
                <div class="account-controls">
                    <a href="#">Forgot Password?</a>
                    <button type="button" id="login-button" name="login-button">Login <i class="fas fa-solid fa-angle-right"></i></button>
                </div>
                <span class="signup-text">Don't have an account yet?<br> <a href="#">Contact Your ColorWrap Agent</a></span>
            </form>
        </div>
        <div class="placeholder-banner" id="banner">
            <img src="https://img.freepik.com/free-vector/abstract-flat-design-background_23-2148450082.jpg?size=626&ext=jpg&ga=GA1.1.1286474015.1708934801&semt=sph" alt="" class="banner">
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#toggle-password').on('click', function() {
                var passwordField = $('#password');
                var passwordToggleIcon = $('#toggle-icon');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    passwordToggleIcon.removeClass('fas fa-eye').addClass('fas fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    passwordToggleIcon.removeClass('fas fa-eye-slash').addClass('fas fa-eye');
                }
            });

            // Handle login button click
            $('#login-button').on('click', function() {
                var email = $('#email').val();
                var password = $('#password').val();
                if (email && password) {
                    $.ajax({
                        url: "{{ route('login') }}",
                        method: 'POST',
                        data: {
                            email: email,
                            password: password,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.redirect) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Login Successful',
                                    text: 'You will be redirected shortly.',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                }).then(function() {
                                    window.location.href = response.redirect;
                                });
                            } else if (response.change_password) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Change Password Required',
                                    text: 'Please change your password.',
                                    showCancelButton: true,
                                    confirmButtonText: 'Change Password',
                                    cancelButtonText: 'Cancel',
                                }).then(function(result) {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('change-password') }}";
                                    }
                                    
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Login Failed',
                                    text: 'Invalid email or password. Please try again.',
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Failed',
                                text: 'Failed to authenticate. Please try again later.',
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'Please enter both email and password.',
                    });
                }
            });
        });
    </script>
</body>
</html>
