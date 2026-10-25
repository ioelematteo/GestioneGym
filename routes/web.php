<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BodyController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\TrainController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Support\Facades\Route;

// LOGIN: max 5 tentativi al minuto per IP (anti-bot / anti forza bruta)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    // Allenamento (in palestra)
    Route::get('allenamento', [TrainController::class, 'index'])->name('train.index');
    Route::get('allenamento/{workout}', [TrainController::class, 'show'])->name('train.show');
    Route::post('allenamento/{workout}', [TrainController::class, 'store'])->name('train.store');

    // Schede
    Route::get('schede/nuova', [WorkoutController::class, 'create'])->name('workouts.create');
    Route::post('schede', [WorkoutController::class, 'store'])->name('workouts.store');
    Route::get('schede/{workout}/modifica', [WorkoutController::class, 'edit'])->name('workouts.edit');
    Route::put('schede/{workout}', [WorkoutController::class, 'update'])->name('workouts.update');
    Route::delete('schede/{workout}', [WorkoutController::class, 'destroy'])->name('workouts.destroy');

    // Storico
    Route::get('storico', [HistoryController::class, 'index'])->name('history.index');
    Route::get('storico/{session}', [HistoryController::class, 'show'])->name('history.show');
    Route::delete('storico/{session}', [HistoryController::class, 'destroy'])->name('history.destroy');

    // Tracker pesi
    Route::get('pesi', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('pesi/{exercise}', [ProgressController::class, 'show'])->name('progress.show');

    // Forma fisica: misure + foto
    Route::get('corpo', [BodyController::class, 'index'])->name('body.index');
    Route::post('corpo/misure', [BodyController::class, 'store'])->name('body.store');
    Route::delete('corpo/misure/{entry}', [BodyController::class, 'destroy'])->name('body.destroy');
    Route::post('corpo/foto', [PhotoController::class, 'store'])->name('photos.store');
    Route::get('corpo/foto/{photo}', [PhotoController::class, 'show'])->name('photos.show');
    Route::delete('corpo/foto/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

    // Coach IA (max 10 domande l'ora: l'API costa)
    Route::get('coach', [CoachController::class, 'index'])->name('coach.index');
    Route::post('coach', [CoachController::class, 'store'])->name('coach.store')->middleware('throttle:10,60');

    // Profilo
    Route::get('profilo', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profilo/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Admin (ruolo Spatie)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('utenti', [UserController::class, 'index'])->name('users.index');
        Route::get('utenti/nuovo', [UserController::class, 'create'])->name('users.create');
        Route::post('utenti', [UserController::class, 'store'])->name('users.store');
        Route::get('utenti/{user}/modifica', [UserController::class, 'edit'])->name('users.edit');
        Route::put('utenti/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('utenti/{user}/blocco', [UserController::class, 'toggle'])->name('users.toggle');
        Route::delete('utenti/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
