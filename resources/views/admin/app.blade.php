<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/logos/cwiicon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/logos/cwiicon.png') }}">
  <title>
    ColorWrap Inc, USA - Orders Management
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
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  

  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/material-dashboard.css?v=3.1.0') }}" rel="stylesheet"/>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FullCalendar CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>


    <style>

      a[aria-expanded="true"] .dropdown-arrow {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
      }

    </style>


@stack('styles')
</head>

<body class="g-sidenav-show  bg-gray-200">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="https://datapluzz.com" target="_blank">
        <img src="{{ asset('assets/img/logos/cwi.png') }}" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold text-white">ColorWrap Inc.</span>
      </a>
    </div>

    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">

        {{-- OVERVIEW --}}
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Overview</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white {{ Route::is('admin.dashboard') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.dashboard') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>

        {{-- USER MANAGEMENT --}}
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Administration</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white collapsed d-flex align-items-center" href="#adminMenu" data-bs-toggle="collapse">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">group</i>
            </div>
            <span class="nav-link-text ms-1">User & Role Management</span>
          </a>
          <div class="collapse" id="adminMenu">
            <ul class="nav flex-column ps-3">
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.customers') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.customers') }}">
                  <i class="material-icons opacity-10">person</i> Clients & Companies
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.admins') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.admins') }}">
                  <i class="material-icons opacity-10">engineering</i> System Admins
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.roles') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.roles') }}">
                  <i class="material-icons opacity-10">admin_panel_settings</i> Roles & Permissions
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- ORDER MANAGEMENT --}}
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Operations</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white collapsed d-flex align-items-center" href="#ordersMenu" data-bs-toggle="collapse">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">rule</i>
            </div>
            <span class="nav-link-text ms-1">Order Management</span>
          </a>
          <div class="collapse" id="ordersMenu">
            <ul class="nav flex-column ps-3">
              <li><a class="nav-link text-white {{ Route::is('admin.reviewOrders') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.reviewOrders') }}"><i class="material-icons opacity-10">visibility</i> Review Orders</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.approvedOrders') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.approvedOrders') }}"><i class="material-icons opacity-10">check_circle</i> Approved Orders</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.declinedOrders') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.declinedOrders') }}"><i class="material-icons opacity-10">block</i> Declined Orders</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.canceledOrders') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.canceledOrders') }}"><i class="material-icons opacity-10">cancel</i> Canceled Orders</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.check_in.index') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.check_in.index') }}"><i class="material-icons opacity-10">list_alt</i> Order Items</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.check_in.start') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.check_in.start') }}"><i class="material-icons opacity-10">start</i> Start Check-In</a></li>
              <li><a class="nav-link text-white" href="{{ route('admin.check_in.print_labels', 1) }}"><i class="material-icons opacity-10">print</i> Print Labels</a></li>
            </ul>
          </div>
        </li>

        {{-- PRODUCTION MANAGEMENT --}}
        <li class="nav-item">
          <a class="nav-link text-white collapsed d-flex align-items-center" href="#productionMenu" data-bs-toggle="collapse">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">precision_manufacturing</i>
            </div>
            <span class="nav-link-text ms-1">Production</span>
          </a>
          <div class="collapse" id="productionMenu">
            <ul class="nav flex-column ps-3">
              <li><a class="nav-link text-white {{ Route::is('admin.productionLines') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.productionLines') }}"><i class="material-icons opacity-10">tune</i> Manage Lines</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.scheduledOrders') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.scheduledOrders') }}"><i class="material-icons opacity-10">event</i> Scheduled Orders</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.scheduledOrdersCalendar') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.scheduledOrdersCalendar') }}"><i class="material-icons opacity-10">calendar_month</i> Calendar</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.manageProduction') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.manageProduction') }}"><i class="material-icons opacity-10">qr_code_scanner</i> Manage Production</a></li>
            </ul>
          </div>
        </li>

        {{-- DEFECTS + PACKAGING + DELIVERY --}}
        <li class="nav-item">
          <a class="nav-link text-white collapsed d-flex align-items-center" href="#logisticsMenu" data-bs-toggle="collapse">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">local_shipping</i>
            </div>
            <span class="nav-link-text ms-1">Logistics</span>
          </a>
          <div class="collapse" id="logisticsMenu">
            <ul class="nav flex-column ps-3">
              <li><a class="nav-link text-white {{ Route::is('admin.defects.index') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.defects.index') }}"><i class="material-icons opacity-10">report_problem</i> Defects</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.defects.reports') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.defects.reports') }}"><i class="material-icons opacity-10">analytics</i> Defect Reports</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.packaging.index') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.packaging.index') }}"><i class="material-icons opacity-10">package</i> Packaging</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.deliveries') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.deliveries') }}"><i class="material-icons opacity-10">local_shipping</i> Delivery</a></li>
            </ul>
          </div>
        </li>

        {{-- INVENTORY --}}
        <li class="nav-item">
          <a class="nav-link text-white collapsed d-flex align-items-center" href="#inventoryMenu" data-bs-toggle="collapse">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">inventory</i>
            </div>
            <span class="nav-link-text ms-1">Inventory</span>
          </a>
          <div class="collapse" id="inventoryMenu">
            <ul class="nav flex-column ps-3">
              <li><a class="nav-link text-white {{ Route::is('admin.inventory.items') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.inventory.items') }}"><i class="material-icons opacity-10">list</i> Items</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.inventory.brands') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.inventory.brands') }}"><i class="material-icons opacity-10">branding_watermark</i> Brands</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.inventory.categories') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.inventory.categories') }}"><i class="material-icons opacity-10">category</i> Categories</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.inventory.warehouses') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.inventory.warehouses') }}"><i class="material-icons opacity-10">warehouse</i> Warehouses</a></li>
              <li><a class="nav-link text-white {{ Route::is('admin.inventory.transfers') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.inventory.transfers') }}"><i class="material-icons opacity-10">compare_arrows</i> Transfers</a></li>
            </ul>
          </div>
        </li>

        {{-- SUPPORT --}}
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Support</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="tel:+16147870056">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">support_agent</i>
            </div>
            <span class="nav-link-text ms-1">DataPluzz Support Desk</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="mailto:info@datapluzz.com">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">mail</i>
            </div>
            <span class="nav-link-text ms-1">Mail Support Ticket</span>
          </a>
        </li>
      </ul>
    </div>

    {{-- LOGOUT --}}
    <div class="sidenav-footer position-absolute w-100 bottom-0">
      <div class="mx-3">
        <a class="btn bg-gradient-primary w-100" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">LOGOUT</a>
      </div>
    </div>
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
  </aside>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
     <nav class="navbar navbar-expand-lg bg-white shadow-sm rounded-bottom mb-4">
      <div class="container-fluid px-3">
          <a href="{{ route('admin.dashboard') }}" class="navbar-brand mb-0 fw-bold"><i class="fa fa-dashboard me-2"></i>Dashboard</a>
          <div class="collapse navbar-collapse justify-content-end">
            @php
              $admin = Auth::guard('admin')->user();
            @endphp
              <ul class="navbar-nav">
                  <li class="nav-item d-flex align-items-center">
                      <a href="#" class="nav-link text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#companyProfileModal">
                          <i class="fa fa-user-circle me-2"></i>
                          <span class="d-sm-inline d-none">
                            @if($admin)
                                Welcome Back, {{ $admin->name }}
                            @else
                                Welcome, Guest
                            @endif
                          </span>
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
                        <i class="fas fa-building me-2"></i> Admin Profile
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Admin Name:</strong> {{ Auth::guard('admin')->user()?->name ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Email:</strong> {{ Auth::guard('admin')->user()?->email ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Joined:</strong> {{ Auth::guard('admin')->user()?->created_at?->format('M d, Y') ?? 'N/A' }}
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

    <!-- End Navbar -->

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
  <script src="{{ asset('assets/js/material-dashboard.min.js?v=3.1.0') }}"></script>

  <script>
    function checkSessionExpired() {
        fetch('{{ route('admin.dashboard') }}', {
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
                    window.location.href = '{{ route('admin.login') }}';
                });
            }
        })
        .catch(error => console.error('Error checking session expiration:', error));
    }

    // Check session expiration every 5 minutes (300,000 ms)
    setInterval(checkSessionExpired, 300000);
  </script>


  <script>
    document.addEventListener('click', function (event) {
      if (event.target && event.target.id === 'confirmDeclineButton') {
          const declineReasonInput = document.querySelector('#declineReason');
          const declineOrderForm = document.querySelector('#declineOrderForm');
          const declineReason = declineReasonInput.value.trim();

          if (!declineReason) {
              alert('Please enter a reason for declining the order.');
              return;
          }

          const confirmation = confirm(`Are you sure you want to decline this order?\nReason: ${declineReason}`);
          if (confirmation) {
              declineOrderForm.submit();
          }
      }
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("[data-bs-toggle='collapse']").forEach(link => {
            link.addEventListener("click", function () {
                const icon = this.querySelector(".dropdown-icon");

                // Check if the target menu is already open
                const targetMenu = document.querySelector(this.getAttribute("href"));
                if (targetMenu.classList.contains("show")) {
                    icon.innerHTML = "expand_more"; // Collapse state
                } else {
                    icon.innerHTML = "expand_less"; // Expanded state
                }
            });
        });
    });
  </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


@stack('scripts')

</body>

</html>