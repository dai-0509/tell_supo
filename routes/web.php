<?php

use App\Http\Controllers\CallLogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KpiTargetController;
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
});

Route::middleware(['auth'])->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::resource('call-logs', CallLogController::class);

    // KPI管理
    Route::resource('kpi-targets', KpiTargetController::class)->parameters(['kpi-targets' => 'kpiTarget']);
    Route::post('kpi-targets/reset', [KpiTargetController::class, 'reset'])->name('kpi-targets.reset');
    
    // KPI計算API（AJAX用）
    Route::post('kpi-targets/calculate-calls', [KpiTargetController::class, 'calculateRecommendedCalls'])->name('kpi-targets.calculate-calls');
    Route::post('kpi-targets/distribute-weekly', [KpiTargetController::class, 'distributeWeeklyTarget'])->name('kpi-targets.distribute-weekly');

    // 既存ページ（TODO: F007実装時にダッシュボードページに置き換え予定）
    Route::view('/calls', 'pages.call-logs.index')->name('calls.index');

    // API endpoints for React components
    Route::get('/api/customers', [CustomerController::class, 'apiIndex'])->name('api.customers.index');
});

require __DIR__.'/auth.php';
