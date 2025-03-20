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
Route::get('/municipalities/{municipality}/financials/edit', [MunicipalityFinancialController::class, 'editFinancials'])->name('municipalities.financials.edit');
Route::put('/municipalities/{municipality}/financials/', [MunicipalityFinancialController::class, 'updateFinancials'])->name('municipalities.financials.update');
Route::get('municipalities/report/{id}/edit', [MunicipalityController::class, 'editReport'])->name('municipalities.report.edit');
Route::put('/municipalities/report/{id}', [MunicipalityController::class, 'updateReport'])->name('municipalities.report.update');
Route::get('/municipalities/{name}/edit', [MunicipalityController::class, 'editContacts'])->name('municipalities.edit');
Route::put('/municipalities/{name}', [MunicipalityController::class, 'updateContacts'])->name('municipalities.update');
Route::get('/municipalities/{name}/reports/create', [MunicipalityController::class, 'createReport'])->name('municipalities.report.create');
Route::post('/municipalities/{name}/reports', [MunicipalityController::class, 'storeReport'])->name('municipalities.report.store');
