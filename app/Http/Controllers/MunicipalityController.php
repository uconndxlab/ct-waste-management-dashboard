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

    public function viewReport($id)
    {
        $municipality = Municipality::findOrFail($id);
        return view('municipalities.view-report', compact('municipality'));
    }

    public function editReport($id)
    {
        $municipality = Municipality::findOrFail($id);
        
        return view('municipalities.edit-report', compact('municipality'));
    }

    public function updateReport(Request $request, $id)
    {
        $municipality = Municipality::findOrFail($id);
        
        $validatedData = $request->validate([
            'bulky_waste' => 'nullable|string',
            'recycling' => 'nullable|string',
            'tipping_fees' => 'nullable|string',
            'admin_costs' => 'nullable|string',
            'hazardous_waste' => 'nullable|string',
            'contractual_services' => 'nullable|string',
            'landfill_costs' => 'nullable|string',
            'total_sanitation_refuse' => 'nullable|string',
            'only_public_works' => 'nullable|string',
            'transfer_station_wages' => 'nullable|string',
            'hauling_fees' => 'nullable|string',
            'curbside_pickup_fees' => 'nullable|string',
            'waste_collection' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $municipality->update($validatedData);
        
        return redirect()->route('municipalities.report', ['id' => $municipality->id])
        ->with('success', 'Report updated successfully');
    }

    public function editContacts($name)
    {
        $townInfo = OverallTownInfo::where('municipality', $name)->firstOrFail();
        return view('municipalities.edit-municipality', compact('townInfo', 'name'));
    }
    
    public function updateContacts(Request $request, $name)
    {
        $townInfo = OverallTownInfo::where('municipality', $name)->firstOrFail();
    
        $validatedData = $request->validate([
            'department' => 'nullable|string',
            'contact_1' => 'nullable|string',
            'title_1' => 'nullable|string',
            'phone_1' => 'nullable|string',
            'email_1' => 'nullable|email',
            'contact_2' => 'nullable|string',
            'title_2' => 'nullable|string',
            'phone_2' => 'nullable|string',
            'email_2' => 'nullable|email',
            'notes' => 'nullable|string',
            'other_useful_notes' => 'nullable|string',
        ]);
    
        $townInfo->update($validatedData);
    
        return redirect()->route('municipalities.view', ['name' => $name])
                         ->with('success', 'Town contact information updated successfully.');
    }
    
    

}
