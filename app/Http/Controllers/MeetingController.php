<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import Str class for UUID generation
class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Meeting::all();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* Validate POST request body */
        $validator = Validator::make($request->all(), [
            'meeting_date' => 'required',
            'title' => 'required',
            'organization_id' => 'required|UUID'
        ]);

        /* Check for validation errors */
        if ($validator->fails()) {
            return response()->json(['message' => 'POST request failed', 'request' => $request->all()], 422);
        }

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $meeting = new Meeting($request->all());
        $meeting->id = $uuid; // Set UUID as id
        $meeting->save();

        return response()->json(['message' => 'New meeting successful', 'meeting' => $meeting], 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Merge the id parameter into the request data
        $request->merge(['id' => $id]);

        // Validate the request body
        $validator = Validator::make($request->all(), [
            'organization_id' => 'sometimes|uuid', // Ensure organization_id is a valid UUID if provided
            'attendance_count' => 'sometimes|nullable|integer', // Ensure attendance_count if provided
            'cafe_count' => 'sometimes|nullable|integer',
            'children_count' => 'sometimes|nullable|integer',
            'donations' => 'sometimes|nullable|numeric',
            'meal_count' => 'sometimes|nullable|integer',
            'newcomers_count' => 'sometimes|nullable|integer',
            'nursery_count' => 'sometimes|nullable|integer',
            'transportation_count' => 'sometimes|nullable|integer',
            'youth_count' => 'sometimes|nullable|integer',
            'title' => 'sometimes|nullable|decimal',
            'meeting_type' => 'sometimes|string',
            'mtg_comp_key' => 'sometimes|nullable|string',
            'announcements_contact' => 'sometimes|nullable|string',
            'av_contact' => 'sometimes|nullable|string',
            'cafe_contact' => 'sometimes|nullable|string',
            'children_contact' => 'sometimes|nullable|string',
            'cleanup_contact' => 'sometimes|nullable|string',
            'closing_contact' => 'sometimes|nullable|string',
            'facilitator_contact' => 'sometimes|nullable|string',
            'greeter_contact1' => 'sometimes|nullable|string',
            'greeter_contact2' => 'sometimes|nullable|string',
            'meal' => 'sometimes|nullable|string',
            'meal_contact' => 'sometimes|nullable|string',
            'notes' => 'sometimes|nullable|string',
            'nursery_contact' => 'sometimes|nullable|string',
            'resource_contact' => 'sometimes|nullable|string',
            'security_contact' => 'sometimes|nullable|string',
            'setup_contact' => 'sometimes|nullable|string',
            'support_contact' => 'sometimes|nullable|string',
            'transportation_contact' => 'sometimes|nullable|string',
            'worship' => 'sometimes|nullable|string',
            'youth_contact' => 'sometimes|nullable|string',
        ]);

        // Validate the $id parameter
        $request->validate([
            'id' => 'required|uuid|exists:meetings,id',
        ]);

        // Check if organization_id is provided
        if ($request->has('organization_id')) {
            return response()->json(['message' => 'Unsupported request. Organization cannot be changed.'], 422);
        }

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['message' => 'Update failed', 'errors' => $validator->errors()], 422);
        }

        // Get the meeting to update
        $meeting = Meeting::find($id);

        // If the meeting doesn't exist, return 404
        if (!$meeting) {
            return response()->json(['message' => 'Meeting not found'], 404);
        }
        // Update values
        $meetingData = $request->except('organization_id', 'id');

        // Check if donation key exists and format it if needed
        if (array_key_exists('donation', $meetingData)) {
            $meeting->donation = number_format(
                $meetingData['donation'],
                2
            );
        }

        // Update values
        if ($meeting->update($request->all())) {
            return response()->json(['message' => 'Update successful', 'meeting' => $meeting], 200);
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
            'id' => 'required|uuid|exists:meetings,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (Meeting::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['message' => 'Destroy Meeting successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['message' => 'Destroy Meeting unsuccessful'], 422);
        }
    }
    public function show(string $id)
    {
        return Meeting::find($id);
    }
    public function search(string $target)
    {
        /**
         * the second parameter is the sql command and we concatenate
         *  % on the front and back of the input variable
         */
        return Meeting::where('meeting_date', 'like', '%' . $target . '%')->get();
    }
}
