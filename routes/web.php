<?php

use App\Http\Controllers\ShipmentController;
use Illuminate\Support\Facades\Route;

// Publik
Route::get('/', fn () => redirect()->route('track'));
Route::get('/lacak', [ShipmentController::class, 'track'])->name('track');

// Admin (tambahkan ->middleware('auth') setelah memasang Laravel Breeze)
Route::prefix('admin')->group(function () {
    Route::resource('shipments', ShipmentController::class);
    Route::post('shipments/{shipment}/events', [ShipmentController::class, 'storeEvent'])->name('shipments.events.store');
    Route::get('shipments/{shipment}/geojson', [ShipmentController::class, 'geojson'])->name('shipments.geojson');
});
