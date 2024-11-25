<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GuestController extends Controller
{
    public function welcome(){
        return view('pages.front.welcome');
    }

    public function getBarangays(Request $request)
    {
        $municipality = $request->municipality;

        // Load the JSON file
        $json = File::get(public_path('assets/back/cebu-city.json'));
        $data = json_decode($json, true);

        // Fetch the barangays for the selected municipality
        if (isset($data['CEBU']['municipality_list'][$municipality])) {
            $barangays = $data['CEBU']['municipality_list'][$municipality]['barangay_list'];
            return response()->json(['barangays' => $barangays]);
        }

        return response()->json(['barangays' => []]);
    }
}
