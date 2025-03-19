<?php

use App\Http\Controllers\MunicipalityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MunicipalityFinancialController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/municipalities', [MunicipalityController::class, 'allMunicipalities'])->name('municipalities.all');
Route::get('/municipalities/{name}', [MunicipalityController::class, 'viewMunicipality'])->name('municipalities.view');
Route::get('/municipalities/report/{id}', [MunicipalityController::class, 'viewReport'])->name('municipalities.report');
Route::get('/municipalities/{municipality}/financials', [MunicipalityFinancialController::class, 'show'])->name('municipalities.financials');
Route::get('municipalities/report/{id}/edit', [MunicipalityController::class, 'editReport'])->name('municipalities.report.edit');
Route::put('/municipalities/report/{id}', [MunicipalityController::class, 'updateReport'])->name('municipalities.report.update');
