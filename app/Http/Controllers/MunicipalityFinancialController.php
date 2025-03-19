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

    public function editFinancials($municipality)
    {
        $financialData = MunicipalityFinancialData::where('municipality', $municipality)->firstOrFail();
        return view('municipalities.edit-financials', compact('financialData'));
    }

    public function updateFinancials(Request $request, $municipality)
    {
        $financialData = MunicipalityFinancialData::where('municipality', $municipality)->firstOrFail();
    
        $validatedData = $request->validate([
            'time_period' => 'nullable|string',
            'population' => 'nullable|integer',
            'size' => 'nullable|string',
            'link' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);
    
        $financialData->update($validatedData);
    
        return redirect()->route('municipalities.financials', ['municipality' => $municipality])
                         ->with('success', 'Financial data updated successfully.');
    }
    

    
}
