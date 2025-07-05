<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactMergeController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Custom field routes
Route::resource('custom-fields', CustomFieldController::class);
Route::post('custom-fields/check-unique-label', [CustomFieldController::class, 'checkUniqueLabel'])->name('custom-fields.check-unique-label');
Route::post('custom-fields/check-unique-label-edit', [CustomFieldController::class, 'checkUniqueLabelForEdit'])->name('custom-fields.check-unique-label-edit');


// Contact routes
Route::post('contacts/check-unique-email', [ContactController::class, 'checkUniqueEmail'])->name('contacts.check-unique-email');
Route::post('contacts/check-unique-email-edit', [ContactController::class, 'checkUniqueEmailForEdit'])->name('contacts.check-unique-email-edit');
Route::get('contacts/get-active-custom-fields', [ContactController::class, 'getActiveCustomFields'])->name('contacts.get-active-custom-fields');
Route::resource('contacts', ContactController::class);
Route::post('/contacts/merge', [ContactMergeController::class, 'merge'])->name('contacts.merge');
