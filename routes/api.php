<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);

#Google Calendar Rotas
Route::get('google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
Route::post('calendar/create', [CalendarController::class, 'createEvent'])->name('calendar.create');
Route::get('calendar/list', [CalendarController::class, 'listEvents'])->name('calendar.list');
