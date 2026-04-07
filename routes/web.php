<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', function () {
            return 'Módulo de usuarios';
        })->name('index');
    });


require __DIR__.'/auth.php';