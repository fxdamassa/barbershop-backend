<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\GoogleController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);

Route::get('google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
// Calendar Routes
Route::post('calendar/create', [CalendarController::class, 'createEvent'])->name('calendar.create');
Route::middleware('auth:sanctum')->get('calendar/list', [CalendarController::class, 'listEvents'])->name('calendar.list');

// Teste de usuário logado
Route::middleware('auth:sanctum')->get('test-auth', function () {
    return auth()->user();
});
Route::middleware('auth:sanctum')->get('/calendar/admin/list', [CalendarController::class, 'listEvents'])->name('calendar.admin.list');
Route::middleware('auth:sanctum')->post('/calendar/admin/create', [CalendarController::class, 'createEvent'])->name('calendar.admin.create');
