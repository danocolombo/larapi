<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import Str class for UUID generation

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Organization::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* Validate POST request body */
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required'
        ]);

        /* Check for validation errors */
        if ($validator->fails()) {
            return response()->json(['message' => 'POST request failed', 'request' => $request->all()], 422);
        }

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $organization = new Organization($request->all());
        $organization->id = $uuid; // Set UUID as id
        $organization->save();

        return response()->json(['message' => 'New organization successful', 'organization' => $organization], 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request body
        $validator = Validator::make($request->all(), [
            'location_id' => 'sometimes|uuid', // Ensure location_id is a valid UUID
        ]);

        // Validate the $id parameter
        $idValidationRules = [
            'id' => 'required|uuid|exists:organizations,id',
        ];

        $request->merge(['id' => $id]);

        $request->validate($idValidationRules);

        // If location_id is provided, validate it as UUID
        if ($request->has('location_id')) {
            $validator->sometimes('location_id', 'uuid', function ($input) {
                return $input->location_id !== null;
            });
        }

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['message' => 'Update failed', 'errors' => $validator->errors()], 422);
        }

        // Get the organization to update
        $organization = Organization::find($id);

        // If the obligation doesn't exist, return 404
        if (!$organization) {
            return response()->json(['message' => 'Organization not found'], 404);
        }

        // Update values
        if ($organization->update($request->all())) {
            return response()->json(['message' => 'Update successful'], 200);
        } else {
            return response()->json(['message' => 'Update failed'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Validate that $id is provided and is a valid UUID
        $validationRules = [
            'id' => 'required|uuid|exists:organizations,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (Organization::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['message' => 'Destroy Organization successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['message' => 'Destroy Organization unsuccessful'], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Organization::find($id);
    }

    public function search(string $name)
    {
        /**
         * the second parameter is the sql command and we concatenate
         *  % on the front and back of the input variable
         */
        return Organization::where('name', 'like', '%' . $name . '%')->get();
    }
}
