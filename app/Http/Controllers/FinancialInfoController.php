<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Municipality;

class FinancialInfoController extends Controller
{
    public function show($municipality)
    {
        $municipalityData = Municipality::where('name', $municipality)->firstOrFail();

        return view('financial-info.show', compact('municipalityData'));
    }
}
