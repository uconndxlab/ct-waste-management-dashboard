<?php

namespace App\Http\Controllers;
use App\Models\Town;


use Illuminate\Http\Request;

class TownController extends Controller
{
    public function index()
    {
        $towns = Town::all();
        return view('towns.all-towns', compact('towns'));
    }

    public function show($id)
    {
        $town = Town::findOrFail($id);
        return view('towns.show-town', compact('town'));
    }
}
