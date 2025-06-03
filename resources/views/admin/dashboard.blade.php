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

  <style>

    a[aria-expanded="true"] .dropdown-arrow {
      transform: rotate(180deg);
      transition: transform 0.3s ease;
    }

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
          <a class="nav-link text-white active bg-gradient-primary" href="{{ route('admin.dashboard') }}">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>

        <!-- USER MANAGEMENT -->
        <li class="nav-item mt-4">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">USER MANAGEMENT</h6>
        </li>
    
        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#userManagementMenu" 
            data-bs-toggle="collapse" aria-expanded="false">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">group</i>
            </div>
            <span class="nav-link-text ms-1">User Management</span>
          </a>

          <!-- Dropdown Menu -->
          <div class="collapse" id="userManagementMenu">
              <ul class="nav flex-column ps-3">
                  <li class="nav-item">
                      <a class="nav-link text-white {{ Route::is('admin.customers') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.customers') }}">
                          <i class="material-icons opacity-10">person</i> Clients & Companies
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link text-white {{ Route::is('admin.admins') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.admins') }}">
                          <i class="material-icons opacity-10">engineering</i> System Administrators
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

        <!-- ORDER REVIEW -->
        <li class="nav-item mt-4">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">ORDER REVIEWS</h6>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#frontDeskMenu" 
            data-bs-toggle="collapse" aria-expanded="false">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="material-icons opacity-10">rule</i>
              </div>
              <span class="nav-link-text ms-1">Orders</span>
          </a>
          <!-- Dropdown Menu -->
          <div class="collapse" id="frontDeskMenu">
              <ul class="nav flex-column ps-3">
                <li class="nav-item">
                  <a class="nav-link text-white {{ Route::is('admin.reviewOrders') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.reviewOrders') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                      <i class="material-icons opacity-10">rule</i>
                    </div>
                    <span class="nav-link-text ms-1">Review Purchase Orders</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white {{ Route::is('admin.approvedOrders') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.approvedOrders') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                      <i class="material-icons opacity-10">check_circle</i>
                    </div>
                    <span class="nav-link-text ms-1">Approved Orders</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white {{ Route::is('admin.declinedOrders') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.declinedOrders') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                      <i class="material-icons opacity-10">block</i>
                    </div>
                    <span class="nav-link-text ms-1">Declined Orders</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white {{ Route::is('admin.canceledOrders') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.canceledOrders') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                      <i class="material-icons opacity-10">cancel</i>
                    </div>
                    <span class="nav-link-text ms-1">Canceled Orders</span>
                  </a>
                </li>
              </ul>
          </div>
        </li>

        <!-- ITEMS CHECK-IN MENU -->
        <li class="nav-item mt-4">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">ITEMS CHECK-IN</h6>
        </li>

        <!-- WAREHOUSE MANAGEMENT MENU -->
        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#warehouseMenu"
            data-bs-toggle="collapse" aria-expanded="false">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">warehouse</i>
            </div>
            <span class="nav-link-text ms-1">Warehouses</span>
          </a>

          <div class="collapse" id="warehouseMenu">
            <ul class="nav flex-column ps-3">
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.warehouse.index') ? 'active bg-gradient-primary' : '' }}"
                  href="{{ route('admin.warehouse.index') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">domain</i>
                  </div>
                  <span class="nav-link-text ms-1">All Warehouses</span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.lots.index') ? 'active bg-gradient-primary' : '' }}"
                  href="{{ route('admin.lots.index') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">layers</i>
                  </div>
                  <span class="nav-link-text ms-1">Lots Management</span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.shelves.index') ? 'active bg-gradient-primary' : '' }}"
                  href="{{ route('admin.shelves.index') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">view_column</i>
                  </div>
                  <span class="nav-link-text ms-1">Shelves Management</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- ITEMS CHECK-IN MENU -->
        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#checkInMenu"
            data-bs-toggle="collapse" aria-expanded="false">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">qr_code_scanner</i>
            </div>
            <span class="nav-link-text ms-1">Items Check-In</span>
          </a>

          <div class="collapse" id="checkInMenu">
            <ul class="nav flex-column ps-3">
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.check_in.index') ? 'active bg-gradient-primary' : '' }}"
                  href="{{ route('admin.check_in.index') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">list_alt</i>
                  </div>
                  <span class="nav-link-text ms-1">Order Items List</span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.check_in.start') ? 'active bg-gradient-primary' : '' }}"
                  href="{{ route('admin.check_in.start') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">start</i>
                  </div>
                  <span class="nav-link-text ms-1">Start Check-In</span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link text-white"
                  href="{{ route('admin.check_in.print_labels', 1) }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">print</i>
                  </div>
                  <span class="nav-link-text ms-1">Print Labels</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#lineSchedulingMenu" 
            data-bs-toggle="collapse" aria-expanded="false">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="material-icons opacity-10">precision_manufacturing</i>
              </div>
              <span class="nav-link-text ms-1">Line Scheduling</span>
              <!-- <i class="material-icons ms-auto dropdown-icon">&#xE313;</i>  -->
          </a>
          <!-- Dropdown Menu -->
          <div class="collapse" id="lineSchedulingMenu">
              <ul class="nav flex-column ps-3">
                <li class="nav-item">
                  <a class="nav-link text-white {{ Route::is('admin.productionLines') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.productionLines') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                      <i class="material-icons opacity-10">precision_manufacturing</i>
                    </div>
                    <span class="nav-link-text ms-1">Manage Lines</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white {{ Route::is('admin.scheduledOrders') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.scheduledOrders') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                      <i class="material-icons opacity-10">pending_actions</i>
                    </div>
                    <span class="nav-link-text ms-1">Scheduled Orders</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white {{ Route::is('admin.scheduledOrdersCalendar') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.scheduledOrdersCalendar') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                      <i class="material-icons opacity-10">today</i>
                    </div>
                    <span class="nav-link-text ms-1">Calendar View</span>
                  </a>
                </li>
              </ul>
          </div>
        </li>

        <!-- PRODUCTION MENU -->
        <li class="nav-item mt-4">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">PRODUCTION MANAGEMENT</h6>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#productionMenu" 
            data-bs-toggle="collapse" aria-expanded="false">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="material-icons opacity-10">qr_code_scanner</i>
              </div>
              <span class="nav-link-text ms-1">Production</span>
              <!-- <i class="material-icons ms-auto dropdown-icon">&#xE313;</i> -->
          </a>
          <!-- Dropdown Menu -->
          <div class="collapse" id="productionMenu">
            <ul class="nav flex-column ps-3">
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.manageProduction') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.manageProduction') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">qr_code_scanner</i>
                  </div>
                  <span class="nav-link-text ms-1">Manage Production</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#defectsMenu" 
            data-bs-toggle="collapse" aria-expanded="false">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="material-icons opacity-10">warning</i>
              </div>
              <span class="nav-link-text ms-1">Defects</span>
          </a>
          <!-- Dropdown Menu -->
          <div class="collapse" id="defectsMenu">
            <ul class="nav flex-column ps-3">
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.defects.index') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.defects.index') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">warning</i>
                  </div>
                  <span class="nav-link-text ms-1">Manage Defects</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.defects.reports') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.defects.reports') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">warning</i>
                  </div>
                  <span class="nav-link-text ms-1">Reports</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- PACKAGING AND DELIVERY MENU -->
        <li class="nav-item mt-4">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">PACKAGING AND DELIVERY</h6>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#packagingMenu" 
            data-bs-toggle="collapse" aria-expanded="false">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="material-icons opacity-10">inventory_2</i>
              </div>
              <span class="nav-link-text ms-1">Packaging</span>
          </a>
          <!-- Dropdown Menu -->
          <div class="collapse" id="packagingMenu">
            <ul class="nav flex-column ps-3">
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.packaging.index') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.packaging.index') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">inventory_2</i>
                  </div>
                  <span class="nav-link-text ms-1">Item Packaging</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#deliveryMenu" 
            data-bs-toggle="collapse" aria-expanded="false">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                  <i class="material-icons opacity-10">local_shipping</i>
              </div>
              <span class="nav-link-text ms-1">Delivery</span>
          </a>
          <!-- Dropdown Menu -->
          <div class="collapse" id="deliveryMenu">
            <ul class="nav flex-column ps-3">
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.deliveries') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.deliveries') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">local_shipping</i>
                  </div>
                  <span class="nav-link-text ms-1">Manage Deliveries</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- <li class="nav-item mt-4">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">INVENTORY MANAGEMENT</h6>
        </li> -->
        
        <!-- <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#inventoryItemsMenu" 
            data-bs-toggle="collapse" aria-expanded="false">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">show_chart</i>
              </div>
              <span class="nav-link-text ms-1">Stock Management</span>
          </a>
          <div class="collapse" id="inventoryItemsMenu">
            <ul class="nav flex-column ps-3">
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.inventory.items') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.inventory.items') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">inventory</i>
                  </div>
                  <span class="nav-link-text ms-1">Inventory Items</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.inventory.brands') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.inventory.brands') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">copyright</i>
                  </div>
                  <span class="nav-link-text ms-1">Brands</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.inventory.categories') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.inventory.categories') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">style</i>
                  </div>
                  <span class="nav-link-text ms-1">Categories</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.inventory.warehouses') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.inventory.warehouses') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">warehouse</i>
                  </div>
                  <span class="nav-link-text ms-1">Warehouses</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Route::is('admin.inventory.transfers') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.inventory.transfers') }}">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">move_down</i>
                  </div>
                  <span class="nav-link-text ms-1">Transfers</span>
                </a>
              </li>
            </ul>
          </div>
        </li> -->

        <li class="nav-item mt-4">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">SYSTEM SUPPORT</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center collapsed" href="#supportItemsMenu" 
            data-bs-toggle="collapse" aria-expanded="false">
              <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="material-icons opacity-10">support</i>
              </div>
              <span class="nav-link-text ms-1">System Support</span>
          </a>
          <!-- Dropdown Menu -->
          <div class="collapse" id="supportItemsMenu">
            <ul class="nav flex-column ps-3">
              <li class="nav-item">
                <a class="nav-link text-white " href="tel:+16147870056">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <!-- <i class="fas fa-boxes"></i> -->
                    <i class="material-icons opacity-10">support_agent</i>
                  </div>
                  <span class="nav-link-text ms-1">DataPluzz 24HR Support</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white " href="mailto:info@datapluzz.com">
                  <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="material-icons opacity-10">contact_support</i>
                  </div>
                  <span class="nav-link-text ms-1">Mail Support Ticket</span>
                </a>
              </li>
            </ul>
          </div>
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
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
  </aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
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

    

    <!-- End Navbar -->

    <!-- Admin Dashboard Widgets: Summary Cards -->
 <div class="container-fluid px-4">
  <div class="row g-4 mb-4">
    <!-- Pending Orders -->
    <div class="col-xl-3 col-sm-6">
      @php $pendingPercent = round(($pendingOrders / max($totalOrders, 1)) * 100, 1); @endphp
      <a href="{{ route('admin.reviewOrders') }}" class="text-decoration-none">
        <div class="card h-100">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
              <i class="material-icons opacity-10">hourglass_empty</i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize text-dark">Pending Orders</p>
              <h4 class="mb-0 text-dark">{{ $pendingOrders }}</h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            <div class="progress-wrapper">
              <div class="progress-info d-flex justify-content-between">
                <span class="text-xs">Progress</span>
                <span class="text-xs font-weight-bold">{{ $pendingPercent }}%</span>
              </div>
              <div class="progress">
                <div class="progress-bar bg-warning" style="width: {{ $pendingPercent }}%" role="progressbar"></div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Approved Orders -->
    <div class="col-xl-3 col-sm-6">
      @php $approvedPercent = round(($approvedOrders / max($totalOrders, 1)) * 100, 1); @endphp
      <a href="{{ route('admin.approvedOrders') }}" class="text-decoration-none">
        <div class="card h-100">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
              <i class="material-icons opacity-10">check_circle</i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize text-dark">Approved Orders</p>
              <h4 class="mb-0 text-dark">{{ $approvedOrders }}</h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            <div class="progress-wrapper">
              <div class="progress-info d-flex justify-content-between">
                <span class="text-xs">Progress</span>
                <span class="text-xs font-weight-bold">{{ $approvedPercent }}%</span>
              </div>
              <div class="progress">
                <div class="progress-bar bg-success" style="width: {{ $approvedPercent }}%" role="progressbar"></div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Canceled Orders -->
    <div class="col-xl-3 col-sm-6">
      @php $canceledPercent = round(($canceledOrders / max($totalOrders, 1)) * 100, 1); @endphp
      <a href="{{ route('admin.canceledOrders') }}" class="text-decoration-none">
        <div class="card h-100">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl mt-n4 position-absolute">
              <i class="material-icons opacity-10">cancel</i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize text-dark">Canceled Orders</p>
              <h4 class="mb-0 text-dark">{{ $canceledOrders }}</h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            <div class="progress-wrapper">
              <div class="progress-info d-flex justify-content-between">
                <span class="text-xs">Progress</span>
                <span class="text-xs font-weight-bold">{{ $canceledPercent }}%</span>
              </div>
              <div class="progress">
                <div class="progress-bar bg-secondary" style="width: {{ $canceledPercent }}%" role="progressbar"></div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Declined Orders -->
    <div class="col-xl-3 col-sm-6">
      @php $declinedPercent = round(($declinedOrders / max($totalOrders, 1)) * 100, 1); @endphp
      <a href="{{ route('admin.declinedOrders') }}" class="text-decoration-none">
        <div class="card h-100">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
              <i class="material-icons opacity-10">highlight_off</i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize text-dark">Declined Orders</p>
              <h4 class="mb-0 text-dark">{{ $declinedOrders }}</h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            <div class="progress-wrapper">
              <div class="progress-info d-flex justify-content-between">
                <span class="text-xs">Progress</span>
                <span class="text-xs font-weight-bold">{{ $declinedPercent }}%</span>
              </div>
              <div class="progress">
                <div class="progress-bar bg-danger" style="width: {{ $declinedPercent }}%" role="progressbar"></div>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>


    <div class="container-fluid px-4">
      <!-- Insights Row: Top Ordered Items & Top Clients -->
      <div class="row mb-4">
        
        <!-- Top 5 Ordered Items -->
        <div class="col-md-12 col-lg-6 mb-4">
          <div class="card h-100 shadow-sm">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
              <h6 class="m-0">
                <i class="fas fa-boxes me-2"></i> Top 5 Ordered Items in {{ now()->format('F Y') }}
              </h6>
              <span class="badge bg-light text-primary">
                {{ number_format($totalOrderedQuantity) }} Items
              </span>
            </div>

            <div class="card-body">
              @if($topOrderedItems->isEmpty())
                <p class="text-muted mb-0">No order data available for {{ now()->format('F Y') }}.</p>
              @else
                <ul class="list-group list-group-flush">
                  @foreach($topOrderedItems as $item)
                    @php
                      $percentage = round(($item->total_quantity / max($totalOrderedQuantity, 1)) * 100, 1);
                    @endphp
                    <li class="list-group-item">
                      <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                          <strong>{{ \Illuminate\Support\Str::limit($item->name, 30) }}</strong>
                          <div class="small text-muted">
                            SKU: {{ $item->sku }} | Qty: {{ number_format($item->total_quantity) }}
                          </div>
                          <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $percentage }}%" role="progressbar"></div>
                          </div>
                        </div>
                        <span class="badge bg-info text-white ms-3">{{ $percentage }}%</span>
                      </div>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        </div>


        <!-- Top Clients This Month -->
        <div class="col-md-12 col-lg-6 mb-4">
          <div class="card h-100 shadow-sm">
            <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
              <div>
                <h6 class="m-0">
                  <i class="fas fa-user-tie me-2"></i> Top Clients This Month
                </h6>
                <small class="text-white-50">
                  Total Monthly: ${{ number_format($topClients->sum('total_spent'), 2) }}
                </small>
              </div>
              <span class="badge bg-white text-info">Sales Leaderboard</span>
            </div>
            <div class="card-body">
              @if($topClients->isEmpty())
                <p class="text-muted">No client data available.</p>
              @else
                <ul class="list-group list-group-flush">
                  @foreach($topClients as $client)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div class="me-auto">
                        <strong>{{ \Illuminate\Support\Str::limit($client->name, 30) }}</strong>
                        <div class="small text-muted">Month: ${{ number_format($client->total_spent, 2) }}</div>
                        <div class="small text-muted">Gross: ${{ number_format($client->gross_spent, 2) }}</div>
                      </div>
                      <span class="badge bg-gradient-info text-white align-self-center">
                        <i class="fas fa-chart-line me-1"></i>
                        {{ round(($client->total_spent / max($client->gross_spent, 1)) * 100, 1) }}%
                      </span>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Monthly Order Trends (Line & Bar Chart) -->
      <div class="row mb-4">
        <!-- Line Chart -->
        <div class="col-lg-6 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
              <h6 class="m-0 text-primary">
                <i class="fas fa-chart-line me-2"></i> Monthly Order Trends (Line Chart)
              </h6>
            </div>
            <div class="card-body" style="position: relative; height: 300px;">
              <canvas id="ordersLineChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Bar Chart -->
        <div class="col-lg-6 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
              <h6 class="m-0 text-primary">
                <i class="fas fa-chart-bar me-2"></i> Monthly Order Trends (Bar Chart)
              </h6>
            </div>
            <div class="card-body" style="position: relative; height: 300px;">
              <canvas id="ordersBarChart"></canvas>
            </div>
          </div>
        </div>
      </div>




  </main>

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

 



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const lineCtx = document.getElementById('ordersLineChart').getContext('2d');
    const barCtx = document.getElementById('ordersBarChart').getContext('2d');

    const chartData = {
      labels: {!! json_encode($orderTrendLabels) !!},
      datasets: [{
        label: 'Orders',
        data: {!! json_encode($orderTrendData) !!},
        borderColor: 'rgba(54, 162, 235, 1)',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        pointBackgroundColor: 'rgba(54, 162, 235, 1)',
        pointRadius: 4,
        tension: 0.3
      }]
    };

    // Line Chart
    new Chart(lineCtx, {
      type: 'line',
      data: chartData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        }
      }
    });

    // Bar Chart
    new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: chartData.labels,
        datasets: [{
          label: 'Orders per Month',
          data: chartData.datasets[0].data,
          backgroundColor: 'rgba(54, 162, 235, 0.7)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1,
          borderRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => ` ${ctx.raw} orders`
            }
          }
        }
      }
    });
  });
</script>



</body>

</html>