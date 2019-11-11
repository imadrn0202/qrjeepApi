<?php

namespace App\Http\Controllers\Api;

use App\Driver;
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use App\Http\Resources\Driver as DriverResource;

class DriverController extends Controller
{

    public function getDriverList() {
        $drivers = Driver::where('user_id', Auth::user()->id)->get();

        return DriverResource::collection($drivers)->values()->all();

    }
    
    public function createDriver(Request $request) {
        $data = $request->all();

        Driver::create([
            'user_id' => Auth::user()->id,
            'first_name' => $data['data']['first_name'],
            'last_name' => $data['data']['last_name'],
            'plate_number' => $data['data']['plate_number']
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}
