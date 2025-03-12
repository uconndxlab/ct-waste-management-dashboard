<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\TownController;

Route::get('/towns', [TownController::class, 'index'])->name('towns.index');
Route::get('/towns/{id}', [TownController::class, 'show'])->name('towns.show');
