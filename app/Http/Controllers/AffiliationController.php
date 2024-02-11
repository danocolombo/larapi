<?php

namespace App\Http\Controllers;

use App\Models\Affiliation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Import Str class for UUID generation
use Illuminate\Http\Request;

class AffiliationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Affiliation::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* Validate POST request body */
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'status' => 'required',
            'person_id' => 'required|UUID',
            'organization_id' => 'required|UUID'
        ]);

        /* Check for validation errors */
        if ($validator->fails()) {
            return response()->json(['message' => 'POST request failed', 'request' => $request->all()], 422);
        }

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $affiliation = new Affiliation($request->all());
        $affiliation->id = $uuid; // Set UUID as id
        $affiliation->save();

        return response()->json(['message' => 'New affiliation successful', 'affiliation' => $affiliation], 200);
    }
}
