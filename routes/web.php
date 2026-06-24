<?php

use Illuminate\Support\Facades\Route;

// Redirect home route to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    return redirect()->route('dashboard');
});

// Dashboard Group
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('/dashboard/users', function () {
    return view('dashboard.users');
})->name('dashboard.users');

// Warehouse Group
Route::get('/warehouse', function () {
    return view('warehouse.index');
})->name('warehouse.index');

Route::get('/warehouse/loading', function () {
    return view('warehouse.loading');
})->name('warehouse.loading');

Route::get('/warehouse/validation', function () {
    return view('warehouse.validation');
})->name('warehouse.validation');

// Product Management
Route::get('/product', function () {
    return view('product.index');
})->name('product.index');

// Inventory / Stock
Route::get('/inventory', function () {
    return view('inventory.index');
})->name('inventory.index');

// Delivery & Logistics Group
Route::get('/delivery', function () {
    return view('delivery.index');
})->name('delivery.index');

Route::get('/delivery/detail', function () {
    return view('delivery.detail');
})->name('delivery.detail');

Route::get('/delivery/surat-jalan', function () {
    return view('delivery.surat-jalan');
})->name('delivery.surat-jalan');

Route::get('/delivery/assign-driver', function () {
    return view('delivery.assign-driver');
})->name('delivery.assign-driver');

Route::get('/delivery/monitoring', function () {
    return view('delivery.monitoring');
})->name('delivery.monitoring');

Route::get('/delivery/incident', function () {
    return view('delivery.incident');
})->name('delivery.incident');

// Driver & Fleet Management
Route::get('/driver', function () {
    return view('driver.index');
})->name('driver.index');

// Proof of Delivery
Route::get('/pod', function () {
    return view('pod.index');
})->name('pod.index');

// Tracking GPS
Route::get('/tracking', function () {
    return view('tracking.index');
})->name('tracking.index');

// Audit Log
Route::get('/audit', function () {
    return view('audit.index');
})->name('audit.index');

