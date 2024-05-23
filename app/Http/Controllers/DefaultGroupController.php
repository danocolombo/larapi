<?php

namespace App\Http\Controllers;

use App\Models\DefaultGroup;
use App\Models\Organization;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DefaultGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, int $page = 1)
    {
        // Check if page is provided in the request (query parameter)
        if ($request->has('page')) {
            $page = $request->query('page');
        }

        // Fetch paginated affiliations based on requested page
        $groups = DefaultGroup::paginate(15, ['*'], 'page', $page);

        return $groups;
    }

    public function find(string $id)
    {
        $group = DefaultGroup::find($id);

        if ($group) {
            // Record found, return JSON with "data" key and record object
            return response()->json([
                'status' => 200,
                'data' => $group
            ], 200);
        } else {
            // Record not found, return 404 with "data" key set to null
            return response()->json([
                'status' => 404,
                'data' => null
            ], 404);
        }
    }
    public function list(string $id)
    {
        // Define validation rules for the ID parameter
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => 'Invalid id'], 422);
        }
        // If validation passes, proceed with the search
        $returnData = DefaultGroup::where('organization_id', '=', $id)->get();
        return response()->json(['status' => 200, 'data' => $returnData], 200);
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
            return response()->json(['status' => 422, 'message' => 'POST request failed', 'errors' => $validator->errors()], 422);
        }

        /* Check if organization_id exists in the organizations table */
        $organizationId = $request->input('organization_id');
        if (!Organization::where('id', $organizationId)->exists()) {
            return response()->json(['status' => 422, 'message' => 'Invalid organization_id'], 422);
        }

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $defaultGrp = new DefaultGroup($request->all());
        $defaultGrp->id = $uuid; // Set UUID as id
        $defaultGrp->save();

        return response()->json(['status' => 200, 'message' => 'New default group successful', 'defaultGroup' => $defaultGrp], 200);
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
            return response()->json(['status' => 404, 'message' => 'Default group not found'], 404);
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
            return response()->json(['status' => 422, 'message' => 'Update failed, check the put body'], 422);
        }

        // Extract only the specified values from the request
        $data = $request->only(['title', 'gender', 'facilitator', 'location']);

        // Update values
        if ($dgroup->update($data)) {
            return response()->json(['status' => 200, 'message' => 'Update successful', 'default_group' => $dgroup], 200);
        } else {
            return response()->json(['status' => 422, 'message' => 'Update failed'], 422);
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
            return response()->json(['status' => 200, 'message' => 'Destroy default group successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['status' => 422, 'message' => 'Destroy default group unsuccessful'], 422);
        }
    }
}
