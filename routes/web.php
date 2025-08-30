<?php

use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\RegionalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MunicipalityFinancialController;

Route::get('/', [MunicipalityController::class, 'showHome']);
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
Route::delete('/municipalities/{name}/reports/{reportId}', [MunicipalityController::class, 'deleteReport'])->name('municipalities.report.delete');
Route::post('/municipalities/compare', [MunicipalityController::class, 'compareMunicipalities'])->name('municipalities.compare');

// Regional routes - specific routes must come before generic ones
Route::get('/regions/counties', [RegionalController::class, 'listCounties'])->name('regions.counties');
Route::get('/regions/planning-regions', [RegionalController::class, 'listPlanningRegions'])->name('regions.planning-regions');
Route::get('/regions/classifications', [RegionalController::class, 'listClassifications'])->name('regions.classifications');
Route::post('/regions/compare', [RegionalController::class, 'compareRegions'])->name('regions.compare');
Route::get('/regions/{type}', [RegionalController::class, 'listRegions'])->name('regions.list');
Route::get('/regions/{regionType}/{regionName}', [RegionalController::class, 'viewRegion'])->name('regions.view');

