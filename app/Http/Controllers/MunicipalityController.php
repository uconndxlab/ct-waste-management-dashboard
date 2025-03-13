<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    // Display a list of all unique municipalities
    public function allMunicipalities(Request $request)
    {
        $query = Municipality::select('name')->groupBy('name');

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $municipalities = $query->get();

        return view('municipalities.view-all', compact('municipalities'));
    }

    // Display a summary page for a specific municipality with links to reports by year
    public function viewMunicipality($name)
    {
        $reports = Municipality::where('name', $name)
            ->orderBy('year')
            ->get();

        return view('municipalities.view-municipality', compact('name', 'reports'));
    }

    // Display a specific report
    public function viewReport($id)
    {
        $municipality = Municipality::findOrFail($id);
        return view('municipalities.view-report', compact('municipality'));
    }
}
