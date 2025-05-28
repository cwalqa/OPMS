<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/logos/cwiicon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/logos/cwiicon.png') }}">
  <title>
    ColorWrap Inc, USA - Client Orders
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pap4aZqY4RfG1IepQeI9bET2IKxkLZ1ayZ+3a8E6E2YjcWzFtf+mXnLd+R+mFxZhQO45LgrPy4vlEv3+H4L22g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/material-dashboard.css?v=3.1.0') }}" rel="stylesheet"/>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


  <!-- Custom CSS for print view -->
	<style>
		@media print {

			.order-info-row {
				display: flex;
				justify-content: space-between; /* Ensure the columns are properly spaced */
			}
			.order-info-row .col-md-3 {
				float: left;
				width: 23%; /* Adjust the width to fit all columns on a single row */
			}
			/* Ensure no page breaks within the order table */
			.table-responsive {
				page-break-inside: avoid;
			}

			/* Hide the Print Order button */
			.btn-primary {
			display: none !important;
			}

			/* Hide the Dashboard navigation link */
			a.nav-link[href*="dashboard"] {
			display: none !important;
			}

			/* Hide the Welcome Back message */
			.nav-link.text-body {
			display: none !important;
			}

			/* Hide the fixed plugin (settings button and options) */
			.fixed-plugin {
			display: none !important;
			}

			/* Additional styles to ensure proper print formatting */
			aside.sidenav, /* Hide the sidebar */
			.navbar,       /* Hide the top navbar */
			.footer {      /* Hide the footer if present */
			display: none !important;
			}

			/* Adjust the main content area to take full width */
			main.main-content {
			margin-left: 0 !important;
			padding: 0 !important;
			width: 100% !important;
			}
		}


    .sidenav {
        z-index: 1020 !important;
    }

    /* Bootstrap modal default z-indexes */
    .modal-backdrop.show {
        z-index: 1040 !important;
    }

    .modal.show {
        z-index: 1050 !important;
    }
	</style>

</head>

<body class="g-sidenav-show  bg-gray-200">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="https://datapluzz.com" target="_blank">
        <img src="{{ asset('assets/img/logos/cwi.png') }}" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold text-white">ColorWrap Inc.</span>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">OVERVIEW</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white {{ Route::is('client.dashboard') ? 'active bg-gradient-primary' : '' }}" href="{{ route('client.dashboard') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>

        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">PURCHASE ORDERS</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white {{ Route::is('client.purchaseorder') ? 'active bg-gradient-primary' : '' }} " href="{{ route('client.purchaseorder') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">receipt_long</i>
            </div>
            <span class="nav-link-text ms-1">New Purchase Order</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white {{ Route::is('client.purchaseOrderHistory') ? 'active bg-gradient-primary' : '' }} " href="{{ route('client.purchaseOrderHistory') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">work_history</i>
            </div>
            <span class="nav-link-text ms-1">Order History</span>
          </a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link text-white {{ Route::is('client.canceledOrderHistory') ? 'active bg-gradient-primary' : '' }} " href="{{ route('client.canceledOrderHistory') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">cancel</i>
            </div>
            <span class="nav-link-text ms-1">Canceled Orders</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white {{ Route::is('client.declinedOrderHistory') ? 'active bg-gradient-primary' : '' }} " href="{{ route('client.declinedOrderHistory') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">remove_circle</i>
            </div>
            <span class="nav-link-text ms-1">Declined Orders</span>
          </a>
        </li> -->

        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">SYSTEM MANAGEMENT</h6>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link text-white {{ Route::is('client.notification') ? 'active bg-gradient-primary' : '' }}" href="{{ route('client.notification') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">notifications</i>
            </div>
            <span class="nav-link-text ms-1">Notifications</span>
          </a>
        </li> -->
        <!-- <li class="nav-item">
          <a class="nav-link text-white {{ Route::is('client.profile') ? 'active bg-gradient-primary' : '' }}" href="{{ route('client.profile') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">person</i>
            </div>
            <span class="nav-link-text ms-1">Company Profile</span>
          </a>
        </li> -->
        <li class="nav-item">
          <a class="nav-link text-white {{ Route::is('client.updatePassword') ? 'active bg-gradient-primary' : '' }}" href="{{ route('client.updatePassword') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">password</i>
            </div>
            <span class="nav-link-text ms-1">Change Password</span>
          </a>
        </li>

        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">SUPPORT</h6>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link text-white " href="tel:+16147870056">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">tty</i>
            </div>
            <span class="nav-link-text ms-1">Call Help Desk</span>
          </a>
        </li> -->
        <li class="nav-item">
          <a class="nav-link text-white " href="mailto:info@datapluzz.com">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">mail</i>
            </div>
            <span class="nav-link-text ms-1">Mail Support Request</span>
          </a>
        </li>
      </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0">
        <div class="mx-3">
            <!-- Logout button triggers the form submission -->
            <a class="btn bg-gradient-primary w-100" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" type="button">LOGOUT</a>
        </div>
    </div>

    <!-- Hidden logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
  </aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
       <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm rounded-bottom mb-4">
      <div class="container-fluid px-3">
          <a href="{{ route('client.dashboard') }}" class="navbar-brand mb-0 fw-bold"><i class="fa fa-dashboard me-2"></i>Dashboard</a>
          <div class="collapse navbar-collapse justify-content-end">
              <ul class="navbar-nav">
                  <li class="nav-item d-flex align-items-center">
                      <a href="#" class="nav-link text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#companyProfileModal">
                          <i class="fa fa-user-circle me-2"></i>
                          Welcome Back, {{ Auth::user()->company_name }}
                      </a>
                  </li>
              </ul>
          </div>
      </div>
  </nav>

    <!-- Company Profile Modal -->
    <div class="modal fade" id="companyProfileModal" tabindex="-1" aria-labelledby="companyProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="companyProfileModalLabel">
                        <i class="fas fa-building me-2"></i> Company Profile
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Company Name:</strong> {{ Auth::user()->company_name }}
                        </li>
                        <li class="list-group-item">
                            <strong>Email:</strong> {{ Auth::user()->email }}
                        </li>
                        <li class="list-group-item">
                            <strong>Contact Person:</strong> {{ Auth::user()->contact_person ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Joined:</strong> {{ Auth::user()->created_at?->format('M d, Y') ?? 'N/A' }}
                        </li>
                    </ul>
                </div>
                <div class="modal-footer justify-content-between">
                    <small class="text-muted">Data synced with your company profile</small>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area -->
        @yield('content')
    <!-- End Content Area -->   

   
  </main>
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="material-icons py-2">settings</i>
    </a>
    <div class="card shadow-lg">
      <div class="card-header pb-0 pt-3">
        <div class="float-start">
          <h5 class="mt-3 mb-0">User Interface Configurator</h5>
          <p>See our dashboard options.</p>
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="material-icons">clear</i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="horizontal dark my-1">
      <div class="card-body pt-sm-3 pt-0">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-gradient-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm">Choose between 2 different sidenav types.</p>
        </div>
        <div class="d-flex">
          <button class="btn bg-gradient-dark px-3 mb-2 active" data-class="bg-gradient-dark" onclick="sidebarType(this)">Dark</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
        <!-- Navbar Fixed -->
        <div class="mt-3 d-flex">
          <h6 class="mb-0">Navbar Fixed</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
          </div>
        </div>
        <hr class="horizontal dark my-3">
        <div class="mt-2 d-flex">
          <h6 class="mb-0">Light / Dark</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--   Core JS Files   -->

  @stack('scripts')

  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
  
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
  <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>


  <script>
    function checkSessionExpired() {
        fetch('{{ route('client.dashboard') }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.redirected) {
                // If the server redirects the request (session expired), go to the new location
                window.location.href = response.url;
                return;
            }

            // Handle JSON response if applicable
            return response.json();
        })
        .then(data => {
            if (data && data.session_expired) {
                // Display SweetAlert and redirect to login
                Swal.fire({
                    title: 'Session Expired',
                    text: 'Your session has expired. Please log in again.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '{{ route('login') }}';
                });
            }
        })
        .catch(error => console.error('Error checking session expiration:', error));
    }

    // Check session expiration every 5 minutes (300,000 ms)
    setInterval(checkSessionExpired, 300000);
</script>

</body>

</html>