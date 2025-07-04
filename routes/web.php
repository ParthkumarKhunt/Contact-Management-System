<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomFieldController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Custom field routes
Route::resource('custom-fields', CustomFieldController::class);
Route::post('custom-fields/check-unique-label', [CustomFieldController::class, 'checkUniqueLabel'])->name('custom-fields.check-unique-label');
Route::post('custom-fields/check-unique-label-edit', [CustomFieldController::class, 'checkUniqueLabelForEdit'])->name('custom-fields.check-unique-label-edit');



