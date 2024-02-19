<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import Str class for UUID generation

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //return Group::all();
        $groups = Group::query()->paginate(perPage: 3);
        return response()->json(['data' => $groups], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* Validate POST request body */
        $validator = Validator::make($request->all(), [
            'grp_comp_key' => 'required',
            'title' => 'required',
            'meeting_id' => 'required'
        ]);

        /* Check for validation errors */
        if ($validator->fails()) {
            return response()->json(['message' => 'POST request failed', 'request' => $request->all()], 422);
        }

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $group = new Group($request->all());
        $group->id = $uuid; // Set UUID as id
        $group->save();

        return response()->json(['message' => 'New group successful', 'group' => $group], 200);
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
            'location' => 'required_without_all:grp_comp_key,title,gender,attendance,facilitator,cofacilitator,notes,meeting_id|string',
            'gender' => 'required_without_all:grp_comp_key,title,location,attendance,facilitator,cofacilitator,notes,meeting_id|string|max:1',
            'attendance' => 'required_without_all:grp_comp_key,title,location,gender,facilitator,cofacilitator,notes,meeting_id|smallInteger',
            'facilitator' => 'required_without_all:grp_comp_key,title,location,gender,attendance,cofacilitator,notes,meeting_id|string',
            'cofacilitator' => 'required_without_all:grp_comp_key,title,location,gender,attendance,facilitator,notes,meeting_id|string',
            'notes' => 'required_without_all:grp_comp_key,title,location,gender,attendance,facilitator,cofacilitator,meeting_id|text',
            'meeting_id' => 'required_without_all:grp_comp_key,title,location,gender,attendance,facilitator,cofacilitator,notes|uuid', // Ensure meeting_id is a valid UUID

        ]);

        // Validate the $id parameter
        $idValidationRules = [
            'id' => 'required|uuid|exists:groups,id',
        ];

        $request->merge(['id' => $id]);

        $request->validate($idValidationRules);

        // // If location_id is provided, validate it as UUID
        // if ($request->has('location_id')) {
        //     $validator->sometimes('location_id', 'uuid', function ($input) {
        //         return $input->location_id !== null;
        //     });
        // }

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['message' => 'Update failed', 'errors' => $validator->errors()], 422);
        }

        // Get the group to update
        $group = Group::find($id);

        // If the obligation doesn't exist, return 404
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        // Update values
        if ($group->update($request->all())) {
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
            'id' => 'required|uuid|exists:groups,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (Group::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['message' => 'Destroy Group successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['message' => 'Destroy Group unsuccessful'], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Group::find($id);
    }
    public function searchByMeetingId(string $id)
    {
        // Define validation rules for the ID parameter
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid id'], 422);
        }
        // If validation passes, proceed with the search
        return Group::where('meeting_id', '=', $id)->get();
    }
}
