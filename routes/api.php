<?php

use App\Http\Controllers\GoogleAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AgendaCortesController;
use App\Http\Controllers\DashboardController;

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

// Middlewares
Route::middleware('auth:sanctum')->get('/user/name', [GoogleController::class, 'getUsername']);

//Agendar corte
Route::middleware('auth:sanctum')->group(function () {
    Route::get('agendar-corte/{data}', [AgendaCortesController::class, 'getBookedTimes']);
    Route::post('agendar-corte', [AgendaCortesController::class, 'salvarAgendamento']);
});

Route::middleware('auth:sanctum')->get('/dashboard/estatisticas', [DashboardController::class, 'estatisticas']);
Route::middleware('auth:sanctum')->get('/dashboard/estatisticas/{ano?}', [DashboardController::class, 'estatisticas']);




