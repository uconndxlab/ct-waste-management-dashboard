<?php

namespace App\Http\Controllers;

use App\Models\OverallTownInfo;
use App\Models\MunicipalityFinancialData;

use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{

    public function allMunicipalities(Request $request)
    {
        $letters = Municipality::selectRaw('SUBSTR(name, 1, 1) as letter')
            ->groupBy('letter')
            ->orderBy('letter')
            ->pluck('letter');
    
        $selectedLetter = $request->input('letter', null);
    
        $search = $request->input('search', '');
    
        $query = Municipality::select('name')
            ->groupBy('name')
            ->orderBy('name');
    
        if ($selectedLetter) {
            $query->where('name', 'like', $selectedLetter . '%');
        }
    
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        $municipalities = $query->get();
    
        return view('municipalities.view-all', compact('municipalities', 'letters', 'selectedLetter', 'search'));
    }
    

    public function viewMunicipality($name)
    {
        $reports = Municipality::where('name', $name)
            ->orderBy('year')
            ->get();
    
        $townInfo = OverallTownInfo::where('municipality', $name)->first();

        $financials = MunicipalityFinancialData::where('municipality', $name)->get();
    
        return view('municipalities.view-municipality', compact('name', 'reports', 'townInfo', 'financials'));
    }    

    // Display a specific report
    public function viewReport($id)
    {
        $municipality = Municipality::findOrFail($id);
        return view('municipalities.view-report', compact('municipality'));
    }
}
