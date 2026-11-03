<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\LiaisonController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SuiviController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/incidents/export', [IncidentController::class, 'export'])->name('incidents.export');
    Route::resource('incidents', IncidentController::class);
    Route::post('/incidents/{incident}/suivis', [SuiviController::class, 'store'])->name('incidents.suivis.store');

    Route::resource('sites', SiteController::class)->except('show');
    Route::resource('equipements', EquipementController::class)->except('show');
    Route::resource('liaisons', LiaisonController::class)->except('show');

    Route::get('/rapports/hebdomadaire', [RapportController::class, 'hebdomadaire'])->name('rapports.hebdomadaire');

    Route::resource('users', UserController::class)->except('show')->middleware('can:admin');
});
