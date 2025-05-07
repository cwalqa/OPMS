<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/logos/cwiicon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/logos/cwiicon.png') }}">
  <title>
    ColorWrap Inc - USA
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/material-dashboard.css?v=3.1.0') }}" rel="stylesheet" />

  <!-- SweetAlert2 CDN -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

 
</head>

<body class="bg-gray-200">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg blur border-radius-xl top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
          <div class="container-fluid ps-2 pe-0">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 " href="{{ route('login.form') }}">
                <img src="{{ asset('assets/img/logos/cwi.png') }}" alt="ColorWrap Inc. Logo" style="height: 50px;">
            </a>
            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon mt-2">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </span>
            </button>
            <div class="collapse navbar-collapse" id="navigation">
              <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    @auth('web')
                        <a class="nav-link d-flex align-items-center me-2 active" aria-current="page"
                          href="{{ route('client.dashboard') }}">
                            <i class="fa fa-chart-pie opacity-6 text-dark me-1"></i>
                            Dashboard
                        </a>
                    @else
                        <a href="javascript:void(0);" 
                          class="nav-link d-flex align-items-center me-2 active"
                          onclick="showLoginAlert()">
                            <i class="fa fa-chart-pie opacity-6 text-dark me-1"></i>
                            Dashboard
                        </a>
                    @endauth
                </li>
            </ul>

            <script>
                function showLoginAlert() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Login Required',
                        text: 'Please log in first to access the dashboard.',
                        confirmButtonText: 'Login',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                          location.reload();
                        }
                    });
                }
            </script>

              <ul class="navbar-nav d-lg-flex d-none">
                <li class="nav-item d-flex align-items-center">
                  <a class="btn btn-outline-primary btn-sm mb-0 me-2" target="_blank" href="https://www.datapluzz.com">Contact ColorWrap Inc</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <!-- End Navbar -->
      </div>
    </div>
  </div>
  <main class="main-content  mt-0">
    <div class="page-header align-items-start min-vh-100" style="background-image: url('{{ asset('assets/img/background.jpg') }}'); background-size: cover; background-position: center;">

      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">
              
                <!-- Content Area -->
                @yield('content')
                <!-- End Content Area -->


            </div>
          </div>
        </div>
      </div>
      <!-- <footer class="footer position-absolute bottom-2 py-2 w-100">
        <div class="container">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-12 col-md-6 my-auto">
              <div class="copyright text-center text-sm text-white text-lg-start">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                made with <i class="fa fa-heart" aria-hidden="true"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold text-white" target="_blank">Creative Tim</a>
                for a better web.
              </div>
            </div>
          </div>
        </div>
      </footer> -->
    </div>
  </main>
  <!--   Core JS Files   -->
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/material-dashboard.min.js?v=3.1.0"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Set the timeout duration in milliseconds (e.g., 15 minutes)
    var timeoutDuration = 15 * 60 * 1000; // 15 minutes

    // Timer variable
    var timeout;

    // Function to reset the inactivity timer
    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(logoutUser, timeoutDuration); // Restart the timer
    }

    // Function to log out the user
    function logoutUser() {
        Swal.fire({
            title: 'Session Expired',
            text: 'You have been logged out due to inactivity.',
            icon: 'warning',
            showConfirmButton: false,
            timer: 3000
        }).then(() => {
            // Perform a POST request to the logout route
            fetch("{{ route('admin.logout') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                // Redirect to the login page after logout
                window.location.href = "{{ route('admin.login.form') }}";
            }).catch(error => {
                console.error('Error logging out:', error);
            });
        });
    }

    // Reset the timer on various user events (mouse movements, key presses, etc.)
    window.onload = resetTimer;
    window.onmousemove = resetTimer;
    window.onkeydown = resetTimer;
</script>

@yield('scripts')

</body>

</html>