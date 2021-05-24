<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    public function index(Request $request){
        Log::info($request);
        //return \response()->json(null);
        return response('Response: “OK”', 200)
            ->header('Content-Type', 'text/plain');
    }
}
