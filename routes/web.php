<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CallLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 架電メーター API
    Route::post('/api/call-logs/increment', [DashboardController::class, 'incrementCall'])->name('api.call-logs.increment');
    Route::post('/api/call-logs/decrement', [DashboardController::class, 'decrementCall'])->name('api.call-logs.decrement');
    
    // 顧客管理
    Route::resource('customers', CustomerController::class);
    Route::get('/api/customers/search', [CustomerController::class, 'search'])->name('api.customers.search');
    
    // 架電履歴
    Route::resource('call-logs', CallLogController::class);
    Route::get('/api/call-logs/search', [CallLogController::class, 'search'])->name('api.call-logs.search');
    Route::get('/api/customers/{customer}/call-logs', [CallLogController::class, 'getCustomerCallLogs'])->name('api.customers.call-logs');
    
    // KPI管理
    Route::get('/kpi-targets', function () {
        return view('kpi-targets.index');
    })->name('kpi-targets.index');
    
    // スクリプト管理
    Route::get('/scripts', function () {
        return view('scripts.index');
    })->name('scripts.index');
});

require __DIR__.'/auth.php';
