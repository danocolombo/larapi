<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingListItemResource;
use App\Models\Meeting;
use App\Models\Group;
use App\Models\Organization;
use App\Models\MeetingListItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import Str class for UUID generation
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Exists;
use PhpParser\Node\Stmt\TryCatch;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $org_id = $request->header('org_id');
        $meetings = Meeting::query();

        // Apply any necessary where clauses
        $meetings->where('organization_id', $org_id);

        // Paginate the results using paginate()
        $paginatedMeetings = $meetings->paginate(perPage: 10);

        return response()->json(['data' => $paginatedMeetings], 200);
    }
    // public function index(Request $request)
    // {
    //     $org_id = $request->header('org_id');
    //     if ($org_id) {
    //         $results = Meeting::where('organization_id', $org_id)->get();
    //         return response()->json(['data' => $results], 200);
    //     } else {
    //         return response()->json(['message' => 'org_id is required'], 422);
    //     }
    // }
    public function getActiveOne(Request $request)
    {
        $org_id = $request->header('org_id');
        if ($org_id) {
            $results = Meeting::where('organization_id', $org_id)->get();
            return response()->json(['data' => $results], 200);
        } else {
            return response()->json(['message' => 'org_id is required'], 422);
        }
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
            'organization_id' => 'required|uuid'
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
        $uuid = Str::uuid()->toString(); // Generate UUID
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
        // Get the meeting
        $meeting = Meeting::find($id);

        // Check if the meeting exists
        if (!$meeting) {
            return response()->json(['message' => 'Meeting not found'], 404);
        }

        // Check if organization_id is provided in the request
        if ($request->has('organization_id')) {
            $organizationId = $request->input('organization_id');
            if (!Organization::where('id', $organizationId)->exists()) {
                return response()->json(['message' => 'Invalid request. Organization ID does not exist'], 422);
            }
        }

        // Proceed with the update operation
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
            'title' => 'sometimes|nullable|string',
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

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => 'Update failed', 'errors' => $validator->errors()], 422);
        }

        // Check if donation key exists and format it if needed
        if ($request->has('donation')) {
            $request->merge(['donation' => number_format($request->input('donation'), 2)]);
        }

        // Update values
        if ($meeting->update($request->all())) {
            return response()->json(['status' => 200, 'message' => 'Update successful', 'data' => $meeting], 200);
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
            'id' => 'required|uuid|exists:meetings,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (Meeting::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['status' => 200, 'message' => 'Destroy Meeting successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['status' => 422, 'message' => 'Destroy Meeting unsuccessful'], 422);
        }
    }
    public function getMeetingAndGroups(string $id)
    {
        // Retrieve the meeting by ID
        $meeting = Meeting::find($id);

        // Check if the meeting exists
        if ($meeting) {
            // Retrieve all groups related to the meeting
            $groups = Group::where('meeting_id', $id)->get();

            // Add the groups to the meeting object
            $meeting->groups = $groups;

            // Return the meeting object with groups
            return response()->json(['status' => 200, 'data' => $meeting], 200);
        } else {
            // Return a 404 response if the meeting is not found
            return response()->json(['status' => 404, 'message' => 'Meeting not found'], 404);
        }
    }


    public function show(string $id)
    {
        $meeting = Meeting::find($id);

        // return Meeting::find($id);
        return response()->json(['status' => 200, 'data' => $meeting], 200);
    }
    public function search(string $target)
    {
        /**
         * the second parameter is the sql command and we concatenate
         *  % on the front and back of the input variable
         */
        $results = Meeting::where('meeting_date', 'like', '%' . $target . '%')->get();
        return response()->json(['status' => 200, 'data' => $results], 200);
    }
    // In your controller
    public function gemini(string $id)
    {
        $meetingDetails = Meeting::find($id)->meetingDetails();

        if ($meetingDetails) {
            return response()->json(['status' => 200, 'data' => $meetingDetails], 200);
        } else {
            return response()->json(['status' => 404, 'message' => 'Meeting not found'], 404);
        }
    }
    public function getMeetingsHistoryPage(Request $request, $page)
    {
        $org_id = $request->header('org_id');
        $meetings = Meeting::query();

        // Apply any necessary where clauses
        $meetings->where('organization_id', $org_id)
            ->where('meeting_date', '<', now()) // Use now() to get the current date and time
            ->orderBy('meeting_date', 'desc');
        // Paginate the results using paginate()
        $paginatedMeetings = $meetings->paginate(perPage: 10, page: $page);

        return response()->json(['status' => 200, 'data' => $paginatedMeetings], 200);
    }
    public function getActiveMeetings(Request $request, $page)
    {
        $org_id = $request->header('org_id');
        $target_date = $request->header('target_date'); // Assuming target_date is received

        $meetings = Meeting::query()
            ->where('organization_id', $org_id)
            ->where('meeting_date', '>=', $target_date)
            ->orderBy('meeting_date', 'ASC')
            ->paginate(perPage: 30, page: $page);

        return response()->json(['status' => 200, 'data' => $meetings], 200);
    }

    public function getMeetingsList(Request $request, $type, $page = 1) // Set default page to 1
    {
        $org_id = $request->header('org_id');
        $target_date = $request->header('target_date');
        $direction = ($type === 'active') ? 'asc' : 'desc';
        $page_value = ($type === 'active') ? 30 : 20;
        // Use paginate to retrieve meetings with pagination
        $meetings = MeetingListItem::select([
            'id',
            'organization_id',
            'meeting_date',
            'meeting_type',
            'title',
            'support_contact',
            'attendance_count',
            'meal_count',
            'meal',
            'worship'
        ])
            ->where('organization_id', $org_id)
            ->when($type === 'active', function ($query) use ($target_date) {
                $query->where('meeting_date', '>=', $target_date);
            })
            ->when($type === 'history', function ($query) use ($target_date) {
                $query->where('meeting_date', '<', $target_date);
            })
            ->orderBy('meeting_date', $direction) // Order by meeting_date ascending (default)
            ->paginate(perPage: $page_value, page: $page); // Paginate with 10 items per page (adjust as needed)

        // Access and use data and pagination information
        $theData = MeetingListItemResource::collection($meetings);
        return response()->json(['status' => 200, 'data' => $theData], 200);
        // return MeetingListItemResource::collection($meetings);
    }
}
