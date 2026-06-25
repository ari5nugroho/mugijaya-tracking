<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/warehouse/validation', [WarehouseController::class, 'validation'])->name('warehouse.validation');
    Route::get('/warehouse/loading', [WarehouseController::class, 'loading'])->name('warehouse.loading');
    Route::resource('warehouse', WarehouseController::class);
    Route::resource('product', ProductController::class);
    Route::resource('category', CategoryController::class);

    // Inventory routes
    Route::get('/inventory', [StockController::class, 'index'])->name('inventory.index');
    Route::put('/inventory/{id}', [StockController::class, 'update'])->name('inventory.update');
    Route::post('/inventory/mutate', [StockController::class, 'mutate'])->name('inventory.mutate');

    // Sidebar dummy stubs to prevent route exceptions
    Route::get('/delivery', function() { return 'Delivery Index'; })->name('delivery.index');
    Route::get('/delivery/assign-driver', function() { return 'Assign Driver'; })->name('delivery.assign-driver');
    Route::get('/delivery/monitoring', function() { return 'Delivery Monitoring'; })->name('delivery.monitoring');
    Route::get('/driver', function() { return 'Driver Index'; })->name('driver.index');
    Route::get('/pod', function() { return 'POD Index'; })->name('pod.index');
    Route::get('/tracking', function() { return 'Tracking Index'; })->name('tracking.index');
    Route::get('/delivery/incident', function() { return 'Incident Report'; })->name('delivery.incident');
    Route::get('/dashboard/users', function() { return 'User Management'; })->name('dashboard.users');
    Route::get('/audit', function() { return 'Audit Log'; })->name('audit.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
