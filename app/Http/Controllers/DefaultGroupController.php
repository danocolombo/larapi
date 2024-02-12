<?php

namespace App\Http\Controllers;

use App\Models\DefaultGroup;
use App\Models\Organization;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import Str class for UUID generation

class DefaultGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DefaultGroup::all();
    }
    public function show(string $id)
    {
        return DefaultGroup::find($id);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* Validate POST request body */
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'organization_id' => 'required',
            'gender' => 'nullable|string|max:1', // Ensure gender is nullable, string, and max length of 1

        ]);

        /* Check for validation errors */
        if ($validator->fails()) {
            return response()->json(['message' => 'POST request failed', 'errors' => $validator->errors()], 422);
        }

        /* Check if organization_id exists in the organizations table */
        $organizationId = $request->input('organization_id');
        if (!Organization::where('id', $organizationId)->exists()) {
            return response()->json(['message' => 'Invalid organization_id'], 422);
        }

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $defaultGrp = new DefaultGroup($request->all());
        $defaultGrp->id = $uuid; // Set UUID as id
        $defaultGrp->save();

        return response()->json(['message' => 'New default group successful', 'defaultGroup' => $defaultGrp], 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Get the default group
        $dgroup = DefaultGroup::find($id);

        // Check if the default group exists
        if (!$dgroup) {
            return response()->json(['message' => 'Default group not found'], 404);
        }

        // Validate the request body
        $validator = Validator::make($request->all(), [
            'title' => 'required_without_all:gender,facilitator,location|string',
            'gender' => 'required_without_all:title,facilitator,location|string|max:1',
            'facilitator' => 'required_without_all:title,gender,location|string',
            'location' => 'required_without_all:title,gender,facilitator|string'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // return response()->json(['message' => 'Update failed', 'errors' => $validator->errors()], 422);
            return response()->json(['message' => 'Update failed, check the put body'], 422);
        }

        // Extract only the specified values from the request
        $data = $request->only(['title', 'gender', 'facilitator', 'location']);

        // Update values
        if ($dgroup->update($data)) {
            return response()->json(['message' => 'Update successful', 'default group' => $dgroup], 200);
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
            'id' => 'required|uuid|exists:default_groups,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (DefaultGroup::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['message' => 'Destroy default group successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['message' => 'Destroy default group unsuccessful'], 422);
        }
    }
}
