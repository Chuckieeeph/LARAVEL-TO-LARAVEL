<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeeScheduleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware(['auth', 'role:Accounting Administrator,Cashier,Accounting Staff'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('students', StudentController::class);
    Route::resource('assessments', AssessmentController::class)->only(['index', 'show']);
    Route::resource('transactions', TransactionController::class)->only(['index']);
});

Route::middleware(['auth', 'role:Accounting Administrator,Accounting Staff'])->group(function (): void {
    Route::resource('fee-schedules', FeeScheduleController::class);
});

Route::middleware(['auth', 'role:Accounting Administrator,Cashier'])->group(function (): void {
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'show']);
});
