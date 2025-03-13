<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\MunicipalityController;

Route::get('/municipalities', [MunicipalityController::class, 'allMunicipalities'])->name('municipalities.all');
Route::get('/municipalities/{name}', [MunicipalityController::class, 'viewMunicipality'])->name('municipalities.view');
Route::get('/municipalities/report/{id}', [MunicipalityController::class, 'viewReport'])->name('municipalities.report');