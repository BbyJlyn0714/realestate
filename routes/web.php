<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
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
    Route::controller(PropertyTypeController::class)->group(function(){
        Route::get('/all/type', 'allType')->name('property.all.type');  
    });
}); // Admin Group

// Agent Group Middleware
Route::middleware(['auth', 'role:agent'])->group(function () {
    Route::get('/agent/dashboard', [AgentController::class, 'dashboard'])->name('agent.dashboard');
    Route::get('/agent/logout', [AgentController::class, 'logout'])->name('agent.logout');
}); // Agent Group

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');