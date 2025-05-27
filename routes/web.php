<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Quickbooks\QuickBooksAuthController;
use App\Http\Controllers\QuickbookCustomerController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DefectsController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseItemController;
use App\Http\Controllers\TransferController;


use App\Http\Controllers\ProductionTrackingController;
use App\Http\Controllers\PackagingController;
use App\Http\Controllers\CheckInController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login page

Route::get('/', function () {
    return redirect()->route('login.form');
})->middleware('redirectCustomer');

/*
|--------------------------------------------------------------------------
| QuickBooks API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('quickbooks')->group(function () {
    Route::get('/authorize', [QuickBooksAuthController::class, 'authorize'])->name('quickbooks.authorize');
    Route::get('/callback', [QuickBooksAuthController::class, 'callback'])->name('quickbooks.callback');
    Route::get('/refresh', [QuickBooksAuthController::class, 'refreshToken'])->name('quickbooks.refresh');
    Route::get('/status', [QuickBooksAuthController::class, 'checkStatus'])->name('quickbooks.status');
    Route::get('/disconnect', [QuickBooksAuthController::class, 'disconnect'])->name('quickbooks.disconnect');
    Route::get('/getAccessTokenByRefreshToken', [QuickBooksAuthController::class, 'getAccessTokenByRefreshToken']);
    Route::get('/getCustomers', [QuickbookCustomerController::class, 'getCustomers']);
});


/*
|--------------------------------------------------------------------------
| Customer Authentication Routes
|--------------------------------------------------------------------------
*/
// Login routes
Route::middleware(['guest:web', 'redirectCustomer'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:customer-login')->name('login');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password reset routes
Route::middleware(['guest:web'])->prefix('password')->name('password.')->group(function () {
    Route::get('/reset', [PasswordResetController::class, 'showLinkRequestForm'])->name('request');
    Route::post('/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('email');
    Route::get('/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('reset');
    Route::post('/reset', [PasswordResetController::class, 'reset'])->name('update');
});


// 2FA routes for customers
Route::middleware(['auth:web'])->group(function () {
    Route::get('/2fa', [LoginController::class, 'show2faForm'])
        ->name('customer.2fa');

    Route::post('/2fa', [LoginController::class, 'verify2fa'])
        ->middleware('throttle:2fa')
        ->name('customer.verify2fa');

    Route::post('/2fa/resend', [LoginController::class, 'resend2fa'])
        ->middleware('throttle:resend-2fa')
        ->name('customer.resend2fa');
});


/*
|--------------------------------------------------------------------------
| Customer Portal Routes (authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', 'redirectCustomer'])->prefix('client')->name('client.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('client.dashboard');
    })->name('dashboard');
    
    // Profile & notifications
    Route::post('/notification', [EstimateController::class, 'store'])->name('notification');
    Route::post('/profile', [EstimateController::class, 'store'])->name('profile');
    Route::get('/update-password', [QuickbookCustomerController::class, 'showUpdatePasswordForm'])->name('updatePasswordForm');
    Route::post('/update-password', [QuickbookCustomerController::class, 'updatePassword'])->name('updatePassword');
    
    // Purchase orders
    Route::get('/purchaseorder', [EstimateController::class, 'create'])->name('purchaseorder');
    Route::get('/order/create', [EstimateController::class, 'create'])->name('estimates.create');
    Route::post('/estimates', [EstimateController::class, 'store'])->name('estimates.store');
    Route::get('/purchaseOrderHistory', [EstimateController::class, 'purchaseOrderHistory'])->name('purchaseOrderHistory');
    Route::get('/viewOrderDetails/{id}', [EstimateController::class, 'viewOrderDetails'])->name('viewOrderDetails');
    Route::get('/canceledOrderHistory', [EstimateController::class, 'canceledOrderHistory'])->name('canceledOrderHistory');
    Route::get('/viewCanceledOrderDetails/{id}', [EstimateController::class, 'viewCanceledOrderDetails'])->name('viewCanceledOrderDetails');
    Route::get('/declinedOrderHistory', [EstimateController::class, 'declinedOrderHistory'])->name('declinedOrderHistory');
    Route::get('/viewDeclinedOrderDetails/{id}', [EstimateController::class, 'viewDeclinedOrderDetails'])->name('viewDeclinedOrderDetails');
    Route::get('/downloadOrderPdf/{id}', [EstimateController::class, 'downloadOrderPdf'])->name('downloadOrderPdf');
    Route::get('/print-order/{id}', [EstimateController::class, 'printOrder'])->name('printOrder');
    Route::post('/{id}/cancel', [EstimateController::class, 'cancelOrder'])->name('cancelOrder');
    Route::put('/update-order/{id}', [EstimateController::class, 'updateOrder'])->name('updateOrder');

    
});

// Estimates/Orders routes
Route::middleware(['auth:web'])->group(function () {
    
});

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/
// Admin login redirect
Route::get('/admin-login', function () {
    return redirect()->route('admin.login.form');
})->middleware('redirectAdmin');


// Admin login routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['guest:admin', 'redirectAdmin'])->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login.form');
        Route::post('/login', [AdminLoginController::class, 'login'])->middleware('throttle:admin-login')->name('login');
    });

    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    // Admin 2FA routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/2fa', [AdminLoginController::class, 'show2faForm'])
            ->name('2fa');

        Route::post('/2fa', [AdminLoginController::class, 'verify2fa'])
            ->middleware('throttle:2fa')
            ->name('verify2fa');

        Route::post('/2fa/resend', [AdminLoginController::class, 'resend2fa'])
            ->middleware('throttle:resend-2fa')
            ->name('resend2fa');
    });
});


/*
|--------------------------------------------------------------------------
| Admin Panel Routes (authenticated)
|--------------------------------------------------------------------------
*/
Route::prefix('admin', )->name('admin.')->middleware(['auth:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User management
    Route::prefix('users')->group(function () {
        // Customers
        Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
        Route::get('/customers/{id}', [AdminController::class, 'customerDetails'])->name('customerDetails');
        Route::put('/customers/{id}', [AdminController::class, 'updateCustomer'])->name('updateCustomer');


        // Admins
        Route::get('/admins', [AdminController::class, 'index'])->name('admins');
        Route::post('/addAdmin', [AdminController::class, 'addAdmin'])->name('addAdmin');
        Route::put('/editAdmin/{id}', [AdminController::class, 'editAdmin'])->name('editAdmin');
        Route::put('/update/{id}', [AdminController::class, 'update'])->name('updateAdmin'); // ✅ Added correctly here
        Route::delete('/deleteAdmin/{id}', [AdminController::class, 'deleteAdmin'])->name('deleteAdmin');
    });
    
    // Admin profile
    Route::put('/{id}/update', [AdminController::class, 'update'])->name('update');
    
    // Role management
    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.delete');
    
    // Order management
    Route::prefix('orders')->group(function () {
        Route::get('/review', [AdminController::class, 'reviewOrders'])->name('reviewOrders');
        Route::post('/{id}/approve', [AdminController::class, 'approveOrder'])->name('approveOrder');
        Route::post('/{id}/decline', [AdminController::class, 'declineOrder'])->name('declineOrder');
        Route::get('/approved', [AdminController::class, 'approvedOrders'])->name('approvedOrders');
        Route::get('/declined', [AdminController::class, 'declinedOrders'])->name('declinedOrders');
        Route::get('/canceled', [AdminController::class, 'canceledOrders'])->name('canceledOrders');
        Route::get('/{id}/view', [AdminController::class, 'viewOrderDetails'])->name('viewOrderDetails');
        Route::get('/approved/{id}', [AdminController::class, 'viewApprovedOrderDetails'])->name('viewApprovedOrderDetails');
        Route::get('/declined/{id}', [AdminController::class, 'viewDeclinedOrderDetails'])->name('viewDeclinedOrderDetails');
        Route::get('/canceled/{id}', [AdminController::class, 'viewCanceledOrderDetails'])->name('viewCanceledOrderDetails');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');

    });
    
    Route::prefix('check-in')->name('check_in.')->group(function () {
        // 🌐 Main Pages
        Route::get('/', [CheckInController::class, 'index'])->name('index');
        Route::get('/start', [CheckInController::class, 'start'])->name('start');
        Route::get('/{estimate}/form', [CheckInController::class, 'show'])->name('show');

        Route::post('/{estimate}/preview', [CheckInController::class, 'preview'])->name('preview');

        Route::post('/{estimate}/process', [CheckInController::class, 'process'])->name('process');

        Route::post('/toggle-status', [CheckInController::class, 'toggleStatus'])->name('toggle_status');

        Route::get('/{estimate}/print-labels', [CheckInController::class, 'printLabels'])->name('print_labels');
        Route::get('/{estimate}/generate-pdf', [CheckInController::class, 'generatePdf'])->name('generate_pdf');

        Route::get('/warehouse/{warehouse}/lots', [CheckInController::class, 'getWarehouseLots'])->name('lots.get');
        Route::get('/warehouse/{warehouse}/shelves', [CheckInController::class, 'getWarehouseShelves'])->name('shelves.get');

        Route::get('/modal/{estimate}/preview', [CheckInController::class, 'previewModal'])->name('preview_modal');
        Route::get('/modal/{estimate}/print-labels', [CheckInController::class, 'printLabelsModal'])->name('print_labels_modal');
        Route::get('/modal/show', [CheckInController::class, 'showModal'])->name('show_modal');
    });


    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        Route::post('/', [WarehouseController::class, 'store'])->name('store');
        Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('update');
        Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])->name('destroy');

        // Lots nested under warehouse
        Route::get('{warehouse}/lots', [WarehouseController::class, 'lots'])->name('lots.index');
        Route::post('{warehouse}/lots', [WarehouseController::class, 'storeLot'])->name('lots.store');
        Route::put('{warehouse}/lots/{lot}', [WarehouseController::class, 'updateLot'])->name('lots.update');
        Route::delete('{warehouse}/lots/{lot}', [WarehouseController::class, 'destroyLot'])->name('lots.destroy');

        // Shelves nested under warehouse
        Route::get('{warehouse}/shelves', [WarehouseController::class, 'warehouseShelves'])->name('shelves.index');
        Route::post('{warehouse}/shelves', [WarehouseController::class, 'storeShelfFromWarehouse'])->name('shelves.store');
        Route::put('{warehouse}/shelves/{shelf}', [WarehouseController::class, 'updateShelf'])->name('shelves.update');
        Route::delete('{warehouse}/shelves/{shelf}', [WarehouseController::class, 'destroyShelf'])->name('shelves.destroy');
        
        // Alternative API routes in warehouse controller (as backup)
        Route::get('/{warehouse}/api/lots', [WarehouseController::class, 'getWarehouseLots'])->name('api.lots');
        Route::get('/{warehouse}/api/shelves', [WarehouseController::class, 'getWarehouseShelves'])->name('api.shelves');
    });

    
    // Production line management
    Route::prefix('production-lines')->group(function () {
        Route::get('/', [AdminController::class, 'manageProductionLines'])->name('productionLines');
        Route::post('/{id}/assign-order', [AdminController::class, 'assignOrderToLine'])->name('assignOrderToLine');
        Route::post('/{id}/update-status', [AdminController::class, 'updateLineStatus'])->name('updateLineStatus');
        Route::post('/add', [AdminController::class, 'addProductionLine'])->name('addProductionLine');
        Route::post('/{id}/edit', [AdminController::class, 'editProductionLine'])->name('editProductionLine');
        Route::delete('/{id}/delete', [AdminController::class, 'deleteProductionLine'])->name('deleteProductionLine');
    });
    
    // Schedule management
    Route::get('/scheduled-orders', [AdminController::class, 'viewScheduledOrders'])->name('scheduledOrders');
    Route::get('/scheduled-orders-calendar', [AdminController::class, 'viewScheduledOrdersCalendar'])->name('scheduledOrdersCalendar');
    Route::post('/schedule/add', [AdminController::class, 'addSchedule'])->name('addSchedule');
    Route::post('/schedule/{id}/edit', [AdminController::class, 'editSchedule'])->name('editSchedule');
    Route::delete('/schedule/{id}/delete', [AdminController::class, 'deleteSchedule'])->name('deleteSchedule');
    
    // Production management
    Route::prefix('production')->group(function () {
        Route::get('/manage', [ProductionController::class, 'viewStartProduction'])->name('manageProduction');
    
    
        // Production control actions
        Route::post('/start/{id}', [ProductionController::class, 'startProduction'])->name('production.start.process');
        Route::post('/pause/{id}', [ProductionController::class, 'pauseProduction'])->name('production.pause');
        Route::post('/resume/{id}', [ProductionController::class, 'resumeProduction'])->name('production.resume');
        Route::post('/complete/{id}', [ProductionController::class, 'completeProduction'])->name('production.complete');
        
        Route::post('/{id}/update-status', [ProductionController::class, 'updateStatus'])->name('updateProductionStatus');
        Route::get('/{log_id}/complete', [ProductionController::class, 'completeProduction'])->name('completeProduction');
        Route::post('/{log_id}/log-notes', [ProductionController::class, 'logNotes'])->name('logProductionNotes');
        Route::get('/calendar', [ProductionController::class, 'viewProductionCalendar'])->name('productionCalendar');
        Route::post('/upload-qr-image', [ProductionController::class, 'uploadQrImage'])->name('production.uploadQrImage');
        Route::get('/select-order-item', [ProductionController::class, 'selectOrderItem'])->name('production.selectOrderItem');
        Route::get('/fetch-scheduled-items', [ProductionController::class, 'fetchScheduledItems'])->name('production.fetchScheduledItems');
        Route::post('/production/logs', [ProductionController::class, 'viewStageLogs'])->name('production.viewLogs');
        Route::get('/production/logs/{id}', [ProductionController::class, 'viewStageLogDetails'])->name('production.viewLogDetails');
        
        // Defect management
    


        Route::post('/production/scan', [ProductionTrackingController::class, 'scanQr']);
        Route::post('/production/update-stage', [ProductionTrackingController::class, 'updateStage']);
    });

    // Grouping under 'admin' if applicable
// Inside your already existing admin group, just keep this block
Route::prefix('defects')->name('defects.')->group(function () {
    Route::get('/', [DefectsController::class, 'index'])->name('index');
    Route::get('/create', [DefectsController::class, 'create'])->name('create');
    Route::post('/', [DefectsController::class, 'store'])->name('store');
    Route::get('/{id}', [DefectsController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [DefectsController::class, 'edit'])->name('edit');
    Route::put('/{id}', [DefectsController::class, 'update'])->name('update');
    Route::post('/{id}/rework', [DefectsController::class, 'markForRework'])->name('rework');
    Route::post('/{id}/discard', [DefectsController::class, 'markForDiscard'])->name('discard');
    Route::get('/reports', [DefectsController::class, 'reports'])->name('reports');
});

    
    // Delivery management
    Route::prefix('deliveries')->group(function () {
        Route::get('/', [DeliveryController::class, 'index'])->name('deliveries');
        Route::get('/create', [DeliveryController::class, 'create'])->name('deliveries.create');
        Route::post('/', [DeliveryController::class, 'store'])->name('deliveries.store');
        Route::get('/{id}/edit', [DeliveryController::class, 'edit'])->name('deliveries.edit');
        Route::put('/{id}', [DeliveryController::class, 'update'])->name('deliveries.update');
        Route::post('/log-notes', [DeliveryController::class, 'logNotes'])->name('deliveries.logNotes');
    });
    
    // Inventory management
    Route::prefix('inventory')->name('inventory.')->group(function () {
        // Items
        Route::get('/items', [ItemsController::class, 'index'])->name('items');
        Route::post('/items/store', [ItemsController::class, 'storeItem'])->name('items.store');
        Route::put('/items/{item}/update', [ItemsController::class, 'updateItem'])->name('items.update');
        Route::get('/items/{item}/edit', [ItemsController::class, 'editItem'])->name('items.edit');
        Route::delete('/items/{item}/delete', [ItemsController::class, 'deleteItem'])->name('items.destroy');
        Route::get('/items/{item}', [ItemsController::class, 'show'])->name('items.show');
        
        // Brands
        Route::get('/brands', [BrandController::class, 'index'])->name('brands');
        Route::post('/brands/store', [BrandController::class, 'store'])->name('brands.store');
        Route::put('/brands/{brand}/update', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand}/delete', [BrandController::class, 'destroy'])->name('brands.destroy');
        
        // Categories
        Route::get('/categories', [CategoryController::class, 'categories'])->name('categories');
        Route::post('/categories/store', [CategoryController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}/update', [CategoryController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}/delete', [CategoryController::class, 'deleteCategory'])->name('categories.destroy');
        
        // Warehouses
        Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses');
        Route::post('/warehouses/store', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::put('/warehouses/update/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::delete('/warehouses/destroy/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');
        
        // Transfers
        Route::get('/transfers', [TransferController::class, 'index'])->name('transfers');
        Route::get('/transfers/create', [TransferController::class, 'create'])->name('transfers.create');
        Route::post('/transfers', [TransferController::class, 'store'])->name('transfers.store');
        Route::get('/transfers/{transfer}', [TransferController::class, 'show'])->name('transfers.show');
        Route::get('/items/{item}/locations', [TransferController::class, 'getItemLocations'])->name('items.locations');
    });
});

/*
|--------------------------------------------------------------------------
| Production Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:admin'])->group(function () {
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/complete/{log_id}', [ProductionController::class, 'completeProduction'])->name('complete');
        Route::post('/log-notes', [ProductionController::class, 'logNotes'])->name('logNotes');
        Route::put('/update/{id}', [ProductionController::class, 'updateProduction'])->name('update');
        Route::get('/download-qr/{log_id}', [ProductionController::class, 'downloadQrCode'])->name('downloadQr');
    });
    
});

/*
|--------------------------------------------------------------------------
| Resource Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:admin'])->group(function () {
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('warehouse-items', WarehouseItemController::class);
});

/*
|--------------------------------------------------------------------------
| Utility Routes
|--------------------------------------------------------------------------
*/
Route::get('/qr-scanner', function () {
    return view('qr-scanner');
})->name('qr.scan');

Route::get('/test-email', function () {
    app(\App\Services\PHPMailerService::class)->send('walker@datapluzz.com', 'Test Email', 'This is a test email.');
});



Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Existing routes
    Route::get('/packaging', [PackagingController::class, 'index'])->name('packaging.index');
    Route::get('/packaging/pending', [PackagingController::class, 'showPendingItems'])->name('packaging.pending');
    Route::post('/packaging/create', [PackagingController::class, 'createTask'])->name('packaging.create');
    Route::post('/packaging/{id}/assign', [PackagingController::class, 'assignTask'])->name('packaging.assign');
    Route::post('/packaging/{id}/status', [PackagingController::class, 'updateStatus'])->name('packaging.update-status');
    Route::get('/packaging/{id}/label', [PackagingController::class, 'generateLabel'])->name('packaging.label');
    Route::get('/packaging/{id}/label/download', [PackagingController::class, 'downloadLabel'])->name('packaging.label.download');
    Route::get('/packaging/{id}', [PackagingController::class, 'show'])->name('packaging.show');
    Route::get('/packaging/bulk', [PackagingController::class, 'bulkPackaging'])->name('packaging.bulk');
    Route::post('/packaging/bulk', [PackagingController::class, 'processBulkPackaging'])->name('packaging.bulk.process');
    Route::get('/packaging/reports', [PackagingController::class, 'reports'])->name('packaging.reports');
    
    // New routes for missing functionality
    Route::get('/packaging/materials', [PackagingController::class, 'customMaterials'])->name('packaging.materials');
    Route::post('/packaging/materials', [PackagingController::class, 'addCustomMaterial'])->name('packaging.materials.add');
    Route::post('/packaging/{id}/materials', [PackagingController::class, 'assignMaterialsToTask'])->name('packaging.materials.assign');
    Route::get('/packaging/inventory', [PackagingController::class, 'checkInventoryLevels'])->name('packaging.inventory');
    Route::post('/packaging/{id}/quality', [PackagingController::class, 'qualityControl'])->name('packaging.quality');
    Route::post('/packaging/{id}/shipping', [PackagingController::class, 'prepareForShipping'])->name('packaging.shipping');
});

// Staff routes for packaging tasks
Route::middleware(['auth'])->prefix('packaging')->name('packaging.')->group(function () {
    Route::get('/dashboard', [PackagingController::class, 'staffDashboard'])->name('staff-dashboard');
    Route::post('/task/{id}/progress', [PackagingController::class, 'updateTaskProgress'])->name('task.progress');
});

// API routes for mobile app
Route::middleware(['auth:api'])->prefix('api/packaging')->name('api.packaging.')->group(function () {
    Route::get('/tasks', [PackagingController::class, 'apiGetAssignedTasks'])->name('tasks');
    Route::post('/task/{id}/status', [PackagingController::class, 'apiUpdateTaskStatus'])->name('task.status');
});


