<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Import Str class for UUID generation
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locs = Location::query()->paginate(perPage: 10);
        return response()->json(['data' => $locs], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* Validate POST request body */
        $validator = Validator::make($request->all(), [
            'street' => 'required_without_all:city,state_prov,postal_code',
            'city' => 'required_without_all:street,state_prov,postal_code',
            'state_prov' => 'required_without_all:street,city,postal_code',
            'postal_code' => 'required_without_all:street,city,state_prov'
        ]);

        /* Check for validation errors */
        if ($validator->fails()) {
            return response()->json(['message' => 'POST request failed', 'request' => $request->all()], 422);
        }

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $location = new Location($request->all());
        $location->id = $uuid; // Set UUID as id
        $location->save();

        return response()->json(['message' => 'New location successful', 'location' => $location], 200);
    }
    public function update(Request $request, string $id)
    {
        // Validate the request body
        $validator = Validator::make($request->all(), [
            'street' => 'required_without_all:city,state_prov,postal_code',
            'city' => 'required_without_all:street,state_prov,postal_code',
            'state_prov' => 'required_without_all:street,city,postal_code',
            'postal_code' => 'required_without_all:street,city,state_prov'

        ]);

        // Validate the $id parameter
        $idValidationRules = [
            'id' => 'required|uuid|exists:locations,id',
        ];

        $request->merge(['id' => $id]);
        $request->validate($idValidationRules);

        // Get the jericho_user to update
        $location = Location::find($id);

        // If the location doesn't exist, return 404
        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        if ($location->update($request->all())) {
            // On success, return updated group with 200 status
            return response()->json([
                'status' => 200,
                'message' => 'Update successful',
                'data' => $location
            ], 200);
        } else {
            // On failure, create error response with 422 status
            return response()->json([
                'status' => 422,
                'message' => 'Update failed',
                'errors' => ['general' => 'Failed to update group.'] // Provide a general error message
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Validate that $id is provided and is a valid UUID
        $validationRules = [
            'id' => 'required|uuid|exists:locations,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (Location::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['message' => 'Destroy Location successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['message' => 'Destroy Location unsuccessful'], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function getLocationById(string $id)
    {
        $loc = Location::find($id);
        if ($loc) {
            // Record found, return JSON with "data" key and record object
            return response()->json([
                'data' => $loc
            ], 200);
        } else {
            // Record not found, return 404 with "data" key set to null
            return response()->json([
                'data' => null
            ], 404);
        }
    }
}
