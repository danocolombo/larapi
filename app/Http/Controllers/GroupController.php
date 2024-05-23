<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //return Group::all();
        $groups = Group::query()->paginate(perPage: 10);
        return response()->json(['data' => $groups], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* Validate POST request body */
        $validator = Validator::make($request->all(), [
            // 'grp_comp_key' => 'required',
            'title' => 'required',
            'meeting_id' => 'required'
        ]);

        /* Check for validation errors */
        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => 'POST request failed', 'request' => $request->all()], 422);
        }
        $gck = $request->get('grp_comp_key', 'default');

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate UUID

        $group = new Group($request->all());
        $group->id = $uuid; // Set UUID as id
        $group->grp_comp_key = $gck;
        $group->save();

        return response()->json(['status' => 200, 'message' => 'New group successful', 'group' => $group], 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request body
        $validator = Validator::make($request->all(), [
            'grp_comp_key' => 'required_without_all:title,location,gender,attendance,facilitator,cofacilitator,notes,meeting_id|string',
            'title' => 'required_without_all:grp_comp_key,location,gender,attendance,facilitator,cofacilitator,notes,meeting_id|string',
            'location' => 'required_without_all:grp_comp_key,title,gender,attendance,facilitator,cofacilitator,notes,meeting_id|nullable|string', // Allow null values
            'gender' => 'required_without_all:grp_comp_key,title,location,attendance,facilitator,cofacilitator,notes,meeting_id|string|max:1',
            'attendance' => 'required_without_all:grp_comp_key,title,location,gender,facilitator,cofacilitator,notes,meeting_id|nullable|integer',
            'facilitator' => 'required_without_all:grp_comp_key,title,location,gender,attendance,cofacilitator,notes,meeting_id|nullable|string',
            'cofacilitator' => 'required_without_all:grp_comp_key,title,location,gender,attendance,facilitator,notes,meeting_id|nullable|string',
            'notes' => 'required_without_all:grp_comp_key,title,location,gender,attendance,facilitator,cofacilitator,meeting_id|nullable|string',
            // 'meeting_id' => 'required_without_all:grp_comp_key,title,location,gender,attendance,facilitator,cofacilitator,notes|uuid', // Ensure meeting_id is a valid UUID
        ]);

        // Validate the $id parameter
        $idValidationRules = [
            'id' => 'required|uuid|exists:groups,id',
        ];

        $request->merge(['id' => $id]);

        $request->validate($idValidationRules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => 'Update failed', 'errors' => $validator->errors()], 422);
        }

        // Get the group to update
        $group = Group::find($id);

        // If the obligation doesn't exist, return 404
        if (!$group) {
            return response()->json(['status' => 404, 'message' => 'Group not found'], 404);
        }

        // Update values
        if ($group->update($request->all())) {
            // On success, return updated group with 200 status
            return response()->json([
                'status' => 200,
                'message' => 'Update successful',
                'data' => $group
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
            'id' => 'required|uuid|exists:groups,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (Group::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['status' => 200, 'message' => 'Destroy Group successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['status' => 422, 'message' => 'Destroy Group unsuccessful'], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $group = Group::find($id);

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
                'data' => null,
                'message' => 'Not found'
            ], 404);
        }
    }
    public function searchByMeetingId(string $id)
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
        $returnData = Group::where('meeting_id', '=', $id)->get();
        return response()->json(['status' => 200, 'data' => $returnData], 200);
    }
}
