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
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/material-dashboard.css?v=3.1.0') }}" rel="stylesheet"/>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    .bg-light-grey {
            background-color: #65676b !important; /* Very light grey */
        }
  </style>

</head>

<body class="g-sidenav-show  bg-gray-200">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-light-grey" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="" target="_blank">
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
          <a class="nav-link text-white active bg-gradient-primary" href="{{ route('client.dashboard') }}">
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
          <a class="nav-link text-white " href="{{ route('client.purchaseorder') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">receipt_long</i>
            </div>
            <span class="nav-link-text ms-1">New Purchase Order</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white " href="{{ route('client.purchaseOrderHistory') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">work_history</i>
            </div>
            <span class="nav-link-text ms-1">Order History</span>
          </a>
        </li>

        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">SYSTEM MANAGEMENT</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white " href="{{ route('client.updatePassword') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">password</i>
            </div>
            <span class="nav-link-text ms-1">Change Password</span>
          </a>
        </li>

        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">SUPPORT</h6>
        </li>
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
          <h6 class="navbar-brand mb-0 fw-bold">Dashboard</h6>
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

    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        @php
            $client = Auth::user();

            $approvedOrders = \App\Models\QuickbooksEstimates::where('status', 'approved')
                ->where('customer_ref', $client->customer_id)
                ->get();

            $totalOrders = $approvedOrders->count();
        @endphp

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">check_circle</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Approved Orders</p>
                        <h4 class="mb-0">{{ $totalOrders }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-success text-sm font-weight-bolder">
                            <i class="material-icons align-middle text-sm">done_all</i>
                        </span> 
                        Ready for Production
                    </p>
                </div>
            </div>
        </div>

        @php
            $client = Auth::user();

            $pendingOrders = \App\Models\QuickbooksEstimates::where(function($query) {
            $query->where('status', 'pending')
                  ->orWhere('status', 'PENDING');
                })
                ->where('customer_ref', $client->customer_id)
                ->get();

            $totalPendingOrders = $pendingOrders->count();
        @endphp

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">hourglass_empty</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Pending Orders</p>
                        <h4 class="mb-0">{{ $totalPendingOrders }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-warning text-sm font-weight-bolder">
                            <i class="material-icons align-middle text-sm">pending_actions</i>
                        </span> 
                        Awaiting Approval
                    </p>
                </div>
            </div>
        </div>

        @php
            $client = Auth::user();

            // Check for both spellings of "cancelled"/"canceled"
            $cancelledOrders = \App\Models\QuickbooksEstimates::where(function($query) {
                    $query->where('status', 'cancelled')
                          ->orWhere('status', 'canceled');
                })
                ->where('customer_ref', $client->customer_id)
                ->get();

            $totalCancelledOrders = $cancelledOrders->count();
        @endphp

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">cancel</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Cancelled Orders</p>
                        <h4 class="mb-0">{{ $totalCancelledOrders }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-danger text-sm font-weight-bolder">
                            <i class="material-icons align-middle text-sm">highlight_off</i>
                        </span> 
                        Order Cancelled
                    </p>
                </div>
            </div>
        </div>

       @php
            $declinedOrders = \App\Models\QuickbooksEstimates::where('status', 'declined')
                ->where('customer_ref', $client->customer_id)
                ->get();

            $totalDeclinedOrders = $declinedOrders->count();
        @endphp

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">thumb_down</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Declined Orders</p>
                        <h4 class="mb-0">{{ $totalDeclinedOrders }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-secondary text-sm font-weight-bolder">
                            <i class="material-icons align-middle text-sm">not_interested</i>
                        </span> 
                        Order Declined
                    </p>
                </div>
            </div>
        </div>
      </div>

      <div class="row mb-4 mt-5">
        @php
            $totalOrdersCount = \App\Models\QuickbooksEstimates::where('customer_ref', $client->customer_id)->count();
            $recentOrders = \App\Models\QuickbooksEstimates::where('customer_ref', $client->customer_id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        @endphp

        <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top-4 p-3">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i> Recent Orders - (Total Orders: {{ $totalOrdersCount }})
                    </h6>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>PO Number</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Documents</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm">{{ $order->purchase_order_number ?? 'N/A' }}</h6>
                                                <small class="text-muted">Estimate ID: {{ $order->qb_estimate_id }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-xs">{{ $order->created_at?->format('M d, Y') ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($order->status == 'approved') bg-success 
                                                @elseif($order->status == 'pending') bg-warning text-dark 
                                                @elseif($order->status == 'declined') bg-danger 
                                                @elseif($order->status == 'cancelled') bg-secondary 
                                                @else bg-info @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                          @if($order->po_document_path)
                                              <div class="d-flex align-items-center gap-2">
                                                  @php
                                                      $extension = pathinfo($order->po_document_path, PATHINFO_EXTENSION);
                                                      $iconClass = match(strtolower($extension)) {
                                                          'pdf' => 'fas fa-file-pdf text-danger',
                                                          'doc', 'docx' => 'fas fa-file-word text-primary',
                                                          'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                                          default => 'fas fa-file-alt text-secondary'
                                                      };
                                                      $fileName = basename($order->po_document_path);
                                                      $fileUrl = asset('storage/' . $order->po_document_path);
                                                  @endphp

                                                  <i class="{{ $iconClass }} fs-5"></i>
                                                  <div class="flex-grow-1">
                                                      <span class="d-block small fw-semibold">{{ $fileName }}</span>
                                                      <a href="{{ $fileUrl }}" class="text-decoration-none small" download="{{ $fileName }}">
                                                          <i class="fas fa-download me-1"></i>Download
                                                      </a>
                                                      @if($extension === 'pdf')
                                                          <span class="mx-2">|</span>
                                                          <a href="{{ $fileUrl }}" target="_blank" class="text-decoration-none small">
                                                              <i class="fas fa-eye me-1"></i>Preview
                                                          </a>
                                                      @endif
                                                  </div>
                                              </div>
                                          @else
                                              <span class="text-muted small">No document</span>
                                          @endif
                                      </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recent orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
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