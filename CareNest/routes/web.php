<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\MotherController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot & Reset Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Mother Registration
Route::get('/register/mother', [AuthController::class, 'showRegisterMother'])->name('register.mother');
Route::post('/register/mother', [AuthController::class, 'registerMother']);

// Midwife Registration
Route::get('/register/midwife', [AuthController::class, 'showRegisterMidwife'])->name('register.midwife');
Route::post('/register/midwife', [AuthController::class, 'registerMidwife']);

// Protected Routes
Route::middleware('auth')->group(function () {
    
    // Change Password Routes (Any authenticated user)
    Route::get('/change-password', [PasswordController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password/verify-current', [PasswordController::class, 'verifyCurrentPassword'])->name('password.verify_current');
    Route::post('/change-password', [PasswordController::class, 'updatePassword'])->name('password.update_auth');
    Route::post('/change-password/send-auth-link', [PasswordController::class, 'sendIdentityAuthLink'])->name('password.send_auth_link');
    
    // Generic Dashboard Redirect to fix route('dashboard') references
    Route::get('/dashboard', function() {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->role === 'provider' || $user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'midwife') return redirect()->route('midwife.dashboard');
        if ($user->role === 'mother') return redirect()->route('mother.dashboard');
        return redirect()->route('login');
    })->name('dashboard');

    // Admin Group
    Route::middleware('role:provider,admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/midwife-requests', [\App\Http\Controllers\AdminController::class, 'midwifeRequests'])->name('midwife-requests');
        Route::post('/midwife-requests/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveMidwife'])->name('midwife-requests.approve');
        Route::post('/midwife-requests/{id}/reject', [\App\Http\Controllers\AdminController::class, 'rejectMidwife'])->name('midwife-requests.reject');

        Route::get('/midwives', [\App\Http\Controllers\AdminController::class, 'manageMidwives'])->name('midwives.index');
        Route::get('/midwives/{id}/edit', [\App\Http\Controllers\AdminController::class, 'editMidwife'])->name('midwives.edit');
        Route::put('/midwives/{id}', [\App\Http\Controllers\AdminController::class, 'updateMidwife'])->name('midwives.update');
        Route::delete('/midwives/{id}', [\App\Http\Controllers\AdminController::class, 'deleteMidwife'])->name('midwives.destroy');

        Route::get('/mothers', [MotherController::class, 'index'])->name('mothers.index');
        Route::get('/mothers/create', [MotherController::class, 'create'])->name('mothers.create');
        Route::post('/mothers', [MotherController::class, 'store'])->name('mothers.store');
        Route::get('/mothers/{id}', [MotherController::class, 'show'])->name('mothers.show');
        Route::get('/mothers/{id}/edit', [MotherController::class, 'edit'])->name('mothers.edit');
        Route::put('/mothers/{id}', [MotherController::class, 'update'])->name('mothers.update');

        Route::get('/children', [\App\Http\Controllers\ChildController::class, 'index'])->name('children.index');
        Route::get('/children/create', [\App\Http\Controllers\ChildController::class, 'create'])->name('children.create');
        Route::post('/children', [\App\Http\Controllers\ChildController::class, 'store'])->name('children.store');
        Route::get('/children/{id}', [\App\Http\Controllers\ChildController::class, 'show'])->name('children.show');
        Route::get('/children/{id}/edit', [\App\Http\Controllers\ChildController::class, 'edit'])->name('children.edit');
        Route::put('/children/{id}', [\App\Http\Controllers\ChildController::class, 'update'])->name('children.update');
        Route::delete('/children/{id}', [\App\Http\Controllers\ChildController::class, 'destroy'])->name('children.destroy');

        Route::get('/immunizations', [\App\Http\Controllers\ImmunizationController::class, 'index'])->name('immunizations.index');
        Route::post('/immunizations/child', [\App\Http\Controllers\ImmunizationController::class, 'storeChildVaccine'])->name('immunizations.child.store');
        Route::post('/immunizations/mother', [\App\Http\Controllers\ImmunizationController::class, 'storeMotherVaccine'])->name('immunizations.mother.store');

        Route::get('/alerts', [\App\Http\Controllers\AlertController::class, 'index'])->name('alerts.index');

        // Triposha Book
        Route::get('/triposha', [\App\Http\Controllers\TriposhaBookController::class, 'index'])->name('triposha.index');
        Route::post('/triposha/batch', [\App\Http\Controllers\TriposhaBookController::class, 'storeBatch'])->name('triposha.store.batch');
        Route::delete('/triposha/{id}', [\App\Http\Controllers\TriposhaBookController::class, 'destroy'])->name('triposha.destroy');

        // Clinic Attendances
        Route::get('/attendances', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendances.index');
        Route::post('/attendances/batch', [\App\Http\Controllers\AttendanceController::class, 'storeBatch'])->name('attendances.store.batch');
        Route::delete('/attendances/{id}', [\App\Http\Controllers\AttendanceController::class, 'destroy'])->name('attendances.destroy');
    });

    // Midwife Group
    Route::middleware('role:midwife')->prefix('midwife')->name('midwife.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/mothers', [MotherController::class, 'index'])->name('mothers.index');
        Route::get('/mothers/create', [MotherController::class, 'create'])->name('mothers.create');
        Route::post('/mothers', [MotherController::class, 'store'])->name('mothers.store');
        Route::get('/mothers/{id}', [MotherController::class, 'show'])->name('mothers.show');
        Route::get('/mothers/{id}/edit', [MotherController::class, 'edit'])->name('mothers.edit');
        Route::put('/mothers/{id}', [MotherController::class, 'update'])->name('mothers.update');

        Route::get('/children', [\App\Http\Controllers\ChildController::class, 'index'])->name('children.index');
        Route::get('/children/create', [\App\Http\Controllers\ChildController::class, 'create'])->name('children.create');
        Route::post('/children', [\App\Http\Controllers\ChildController::class, 'store'])->name('children.store');
        Route::get('/children/{id}', [\App\Http\Controllers\ChildController::class, 'show'])->name('children.show');
        Route::get('/children/{id}/edit', [\App\Http\Controllers\ChildController::class, 'edit'])->name('children.edit');
        Route::put('/children/{id}', [\App\Http\Controllers\ChildController::class, 'update'])->name('children.update');

        Route::get('/immunizations', [\App\Http\Controllers\ImmunizationController::class, 'index'])->name('immunizations.index');
        Route::post('/immunizations/child', [\App\Http\Controllers\ImmunizationController::class, 'storeChildVaccine'])->name('immunizations.child.store');
        Route::post('/immunizations/mother', [\App\Http\Controllers\ImmunizationController::class, 'storeMotherVaccine'])->name('immunizations.mother.store');

        Route::get('/alerts', [\App\Http\Controllers\AlertController::class, 'index'])->name('alerts.index');

        // Triposha Book
        Route::get('/triposha', [\App\Http\Controllers\TriposhaBookController::class, 'index'])->name('triposha.index');
        Route::post('/triposha/batch', [\App\Http\Controllers\TriposhaBookController::class, 'storeBatch'])->name('triposha.store.batch');
        Route::delete('/triposha/{id}', [\App\Http\Controllers\TriposhaBookController::class, 'destroy'])->name('triposha.destroy');

        // Clinic Attendances
        Route::get('/attendances', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendances.index');
        Route::post('/attendances/batch', [\App\Http\Controllers\AttendanceController::class, 'storeBatch'])->name('attendances.store.batch');
        Route::delete('/attendances/{id}', [\App\Http\Controllers\AttendanceController::class, 'destroy'])->name('attendances.destroy');
    });

    // Mother Group
    Route::middleware('role:mother')->prefix('mother')->name('mother.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/profile', [MotherController::class, 'profile'])->name('profile');
        Route::get('/mothers/{id}', [MotherController::class, 'show'])->name('mothers.show');
        Route::put('/mothers/{id}', [MotherController::class, 'update'])->name('mothers.update');

        Route::get('/children', [\App\Http\Controllers\ChildController::class, 'index'])->name('children.index');
        Route::get('/children/{id}', [\App\Http\Controllers\ChildController::class, 'show'])->name('children.show');

        Route::get('/immunizations', [\App\Http\Controllers\ImmunizationController::class, 'index'])->name('immunizations.index');
        
        Route::get('/notifications', [\App\Http\Controllers\AlertController::class, 'index'])->name('notifications.index');
    });
});
