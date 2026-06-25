<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

// ============================================================
// Public Routes
// ============================================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================================================
// Dashboard (auth + verified)
// ============================================================
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// ============================================================
// Authenticated Routes
// ============================================================
Route::middleware('auth')->group(function () {

    // -- Profile --
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================
    // Warehouse Module (Owner, Admin, Staff Gudang)
    // =========================================================
    Route::middleware('permission:warehouse.view')->group(function () {
        Route::get('/warehouse/validation', [WarehouseController::class, 'validation'])->name('warehouse.validation');
        Route::get('/warehouse/loading', [WarehouseController::class, 'loading'])->name('warehouse.loading');
    });
    Route::resource('warehouse', WarehouseController::class)->middleware('permission:warehouse.view');

    // =========================================================
    // Category Module (Owner, Admin)
    // =========================================================
    Route::resource('category', CategoryController::class)->middleware('permission:category.view');

    // =========================================================
    // Product Module (Owner, Admin, Staff Gudang)
    // =========================================================
    Route::resource('product', ProductController::class)->middleware('permission:product.view');

    // =========================================================
    // Inventory Module (Owner, Admin, Staff Gudang)
    // =========================================================
    Route::middleware('permission:inventory.view')->group(function () {
        Route::get('/inventory', [StockController::class, 'index'])->name('inventory.index');
        Route::put('/inventory/{id}', [StockController::class, 'update'])->name('inventory.update');
        Route::post('/inventory/mutate', [StockController::class, 'mutate'])->name('inventory.mutate');
    });

    // =========================================================
    // User Management (Owner only)
    // =========================================================
    Route::middleware('role:Owner')->group(function () {
        Route::get('/dashboard/users', [UserController::class, 'index'])->name('users.index');
        Route::put('/dashboard/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');

        Route::resource('roles', RoleController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // =========================================================
    // Audit Log (Owner only)
    // =========================================================
    Route::middleware('role:Owner')->group(function () {
        Route::get('/audit', function () { return view('audit.index'); })->name('audit.index');
    });

    // =========================================================
    // Delivery & Logistics (Owner, Admin)
    // =========================================================
    Route::middleware('permission:delivery.view')->group(function () {
        Route::get('/delivery', function () { return 'Delivery Index'; })->name('delivery.index');
        Route::get('/delivery/monitoring', function () { return 'Delivery Monitoring'; })->name('delivery.monitoring');
    });
    Route::middleware('permission:delivery.create')->group(function () {
        Route::get('/delivery/assign-driver', function () { return 'Assign Driver'; })->name('delivery.assign-driver');
    });
    Route::middleware('role:Owner|Admin')->group(function () {
        Route::get('/delivery/incident', function () { return 'Incident Report'; })->name('delivery.incident');
    });

    // =========================================================
    // Driver & GPS (Owner, Admin, Driver)
    // =========================================================
    Route::middleware('permission:driver.view')->group(function () {
        Route::get('/driver', function () { return 'Driver Index'; })->name('driver.index');
    });
    Route::middleware('permission:gps.view')->group(function () {
        Route::get('/tracking', function () { return 'Tracking Index'; })->name('tracking.index');
    });

    // =========================================================
    // Proof of Delivery (Driver only)
    // =========================================================
    Route::middleware('role:Owner|Admin|Driver')->group(function () {
        Route::get('/pod', function () { return 'POD Index'; })->name('pod.index');
    });
});

require __DIR__.'/auth.php';
