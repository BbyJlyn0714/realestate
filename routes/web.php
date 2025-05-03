<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AmenitiesController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [UserController::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::get('/user/logout', [UserController::class, 'logout'])->name('user.logout');
    Route::get('/user/change/password', [UserController::class, 'changePassword'])->name('user.change.password');
    Route::post('/user/profile/update', [UserController::class, 'update'])->name('user.profile.update');
    Route::post('/user/password/update', [UserController::class, 'updatePassword'])->name('user.password.update');
});

require __DIR__.'/auth.php';

// Admin Group Middleware
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/admin/change/password', [AdminController::class, 'changePassword'])->name('admin.change.password');
    Route::post('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/admin/password/update', [AdminController::class, 'updatePassword'])->name('admin.password.update');

    // Property Type All Route
    Route::controller(PropertyTypeController::class)->group(function () {
        Route::get('/all/type', 'allType')->name('property.all.type');
        Route::get('/add/type', 'create')->name('property.add.type');
        Route::get('/edit/type/{id}', 'edit')->name('property.edit.type');
        Route::get('/delete/type/{id}', 'delete')->name('property.delete.type');
        Route::post('/add/type', 'store')->name('property.store.type');
        Route::post('/update/type', 'update')->name('property.update.type');
    });

    // Amenities All Route
    Route::controller(AmenitiesController::class)->group(function () {
        Route::get('/all/amenities', 'index')->name('amenities.index');
        Route::get('/add/amenities', 'create')->name('amenities.add');
        Route::get('/edit/amenities/{id}', 'edit')->name('amenities.edit');
        Route::get('/delete/amenities/{id}', 'delete')->name('amenities.delete');
        Route::post('/add/amenities', 'store')->name('amenities.store');
        Route::post('/update/amenities', 'update')->name('amenities.update');
    });

    // Property All Route
    Route::controller(PropertyController::class)->group(function () {
        Route::get('/all/property', 'index')->name('property.index');
        Route::get('/add/property', 'create')->name('property.add');
        Route::get('/edit/property/{property}', 'edit')->name('property.edit');
        Route::get('/delete/property/multiImage/{img}', 'deleteMultiImage')->name('property.delete.multiimage');
        Route::post('/add/property', 'store')->name('property.store');
        Route::post('/update/property', 'update')->name('property.update');
        Route::post('/update/property/thumbnail', 'updateThumbnail')->name('property.update.thumbnail');
        Route::post('/update/property/multiImage', 'updateMultiImage')->name('property.update.multiimage');
        Route::post('/store/property/multiImage', 'storeMultiImage')->name('property.store.multiimage');
        Route::post('/update/property/facilities', 'updatePropertyFacilities')->name('update.property.facilities');
        Route::get('/delete/property/{property}', 'delete')->name('property.delete');
        Route::get('/details/property/{property}', 'detailsProperty')->name('property.details');
        Route::get('/inactive/property/{property}', 'inactiveProperty')->name('     ');
        Route::get('/active/property/{property}', 'activeProperty')->name('property.active');
    });
}); // Admin Group

// Agent Group Middleware
Route::middleware(['auth', 'role:agent'])->group(function () {
    Route::get('/agent/dashboard', [AgentController::class, 'dashboard'])->name('agent.dashboard');
    Route::get('/agent/logout', [AgentController::class, 'logout'])->name('agent.logout');
    Route::get('/agent/profile', [AgentController::class, 'profile'])->name('agent.profile');
    Route::post('/agent/profile', [AgentController::class, 'profileUpdate'])->name('agent.profile.update');
    Route::get('/agent/change/password', [AgentController::class, 'changePaswword'])->name('agent.change.password');
    Route::post('/agent/update/password', [AgentController::class, 'updatePassword'])->name('agent.update.password');
}); // Agent Group

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login')->middleware('redirectIfAuthenticated');
Route::get('/agent/login', [AgentController::class, 'login'])->name('agent.login')->middleware('redirectIfAuthenticated');
Route::post('/agent/register', [AgentController::class, 'register'])->name('agent.register');
