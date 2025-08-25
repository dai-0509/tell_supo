<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 顧客管理
    Route::get('/customers', function () {
        return view('customers.index');
    })->name('customers.index');
    
    // 架電履歴
    Route::get('/call-logs', function () {
        return view('call-logs.index');
    })->name('call-logs.index');
    
    // KPI管理
    Route::get('/kpi', function () {
        return view('kpi.index');
    })->name('kpi.index');
});

require __DIR__.'/auth.php';
