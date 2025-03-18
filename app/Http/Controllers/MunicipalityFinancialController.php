<?php

namespace App\Http\Controllers;

use App\Models\MunicipalityFinancialData;
use Illuminate\Http\Request;

class MunicipalityFinancialController extends Controller
{
    public function show($municipality)
    {

        $financialData = MunicipalityFinancialData::where('municipality', $municipality)->first();

        return view('municipalities.financials', compact('financialData'));
    }
}
