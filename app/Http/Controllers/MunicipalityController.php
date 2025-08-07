<?php

namespace App\Http\Controllers;

use App\Models\OverallTownInfo;
use App\Models\MunicipalityFinancialData;
use App\Models\Municipality;
use App\Models\TownClassification;
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

        // Get 'Filter by Index' parameters
        $regionType = $request->input('region_type');
        $geographicalRegion = $request->input('geographical_region');
        $county = $request->input('county');
    
        $query = Municipality::query() // Eloquent builder
            ->select('municipalities.name')
            ->leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality');
    
        if ($selectedLetter) {
            $query->where('municipalities.name', 'like', $selectedLetter . '%');
        }
    
        if ($search) {
            $query->where('municipalities.name', 'like', '%' . $search . '%');
        }

        if ($regionType) {
            $query->where('town_classifications.region_type', $regionType);
        }

        if ($geographicalRegion) {
            $query->where('town_classifications.geographical_region', $geographicalRegion);
        }
        if ($county) {
            $query->where('town_classifications.county', $county);
        }

        $query->groupBy('municipalities.name')
          ->orderBy('municipalities.name');

        $municipalities = $query->get();
        
        // Get unique values for dropdowns
        $regionTypes = TownClassification::distinct()->orderBy('region_type')->pluck('region_type');
        $geographicalRegions = TownClassification::distinct()->orderBy('geographical_region')->pluck('geographical_region');
        $counties = TownClassification::distinct()->orderBy('county')->pluck('county');
    
        return view('municipalities.view-all', compact(
            'municipalities', 
            'letters', 
            'selectedLetter', 
            'search',
            'regionTypes',
            'geographicalRegions',
            'counties',
            'regionType',
            'geographicalRegion',
            'county'
        ));
    }

    public function showHome()
    {
        $municipalities = Municipality::select('name', 'href')->get();
        return view('welcome', compact('municipalities'));
    }
    

    public function viewMunicipality($name)
    {
        $municipality = Municipality::where('name', $name)->firstOrFail();

        $reports = Municipality::where('name', $name)->orderBy('year')->get();

        $townInfo = OverallTownInfo::where('municipality', $name)->first();
        
        $townClassification = TownClassification::where('municipality', $name)->first();
        
        $financials = MunicipalityFinancialData::where('municipality', $name)->get();
        
        $financialData = MunicipalityFinancialData::where('municipality', $name)->firstOrFail();

        return view('municipalities.view-municipality', compact('name', 'reports', 'townInfo', 'financials', 'financialData', 'townClassification', 'municipality'));
    }

    public function viewReport($id)
    {
        $municipality = Municipality::findOrFail($id);
        return view('municipalities.reports.view-report', compact('municipality'));
    }

    public function editReport($id)
    {
        $municipality = Municipality::findOrFail($id);
        
        return view('municipalities.reports.edit-report', compact('municipality'));
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
                         ->with('success', 'Town Contact Information Updated Successfully.');
    }
    
    public function createReport($name)
    {
        return view('municipalities.reports.create-report', compact('name'));
    }
    
    public function storeReport(Request $request, $name)
    {
        $validatedData = $request->validate([
            'year' => 'required|integer',
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
    
        $municipality = Municipality::create(array_merge($validatedData, ['name' => $name]));
    
        return redirect()->route('municipalities.view', ['name' => $name])
            ->with('success', 'New Report Added Successfully.');
    }

    public function deleteReport($name, $reportId)
    {
        $report = Municipality::where('name', $name)->where('id', $reportId)->firstOrFail();
        $report->delete();
    
        return redirect()->route('municipalities.view', ['name' => $name])->with('success', 'Report Deleted Successfully.');
    }

    public function compareMunicipalities(Request $request)
    {
        $request->validate([
            'municipalities' => 'required|array|size:2',
        ], [
            'municipalities.size' => 'You must select exactly two municipalities for comparison.',
        ]);

        // Get the most recent record for each municipality
        $municipalities = collect($request->municipalities)->map(function ($name) {
            return Municipality::where('name', $name)
                ->orderBy('year', 'desc')
                ->first();
        })->filter(); // Remove any null results

        // Ensure we have exactly 2 municipalities
        if ($municipalities->count() !== 2) {
            return back()->withErrors(['municipalities' => 'Could not find data for the selected municipalities.']);
        }

        return view('municipalities.compare', compact('municipalities'));
    }
    
    
    

}
