<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    // Display a list of all unique municipalities
    public function allMunicipalities()
    {
        $municipalities = Municipality::select('name')
            ->groupBy('name')
            ->get();

        return view('municipalities.view-all', compact('municipalities'));
    }

    // Display a summary page for a specific municipality with links to reports by year
    public function viewMunicipality($name)
    {
        $reports = Municipality::where('name', $name)
            ->orderBy('year', 'desc')
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
