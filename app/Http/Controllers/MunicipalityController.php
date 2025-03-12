<?php

namespace App\Http\Controllers;

use App\Models\Municipality;

class MunicipalityController extends Controller
{
    // Display a list of all municipalities
    public function allMunicipalities()
    {
        $municipalities = Municipality::all();
        return view('municipalities.view-all', compact('municipalities'));
    }
 
    // Display a single municipality's details
    public function show($id)
    {
        $municipality = Municipality::findOrFail($id);
        return view('municipalities.show', compact('municipality'));
    }
}
