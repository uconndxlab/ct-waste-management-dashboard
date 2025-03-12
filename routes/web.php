<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\MunicipalityController;

Route::get('/municipalities', [MunicipalityController::class, 'allMunicipalities'])->name('municipalities.view-all');
Route::get('/municipalities/{id}', [MunicipalityController::class, 'show'])->name('municipalities.show');
