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
    public function index(string $org, Request $request, string $historic = null, string $active = null, string $direction = null)
    {
        $org_id = $org;
        $historic = $request->query('historic'); // default direction to asc
        $active = $request->query('active'); // default direction to desc
        $direction = $request->query('direction');
        $perPage = 10; // Meetings per page
        $validDirections = ['asc', 'desc']; // Allowed sorting directions

        if (!$direction) {
            $direction = ($historic) ? 'desc' : 'asc'; // Default to desc for historic, asc for active
        }
        // Validate direction parameter
        if (!in_array(strtolower($direction), $validDirections)) {
            return response()->json([
                'status' => 400, // Bad Request
                'active' => $active,
                'historic' => $historic,
                'org' => $org,
                'direction' => $direction,
                'message' => 'Invalid direction. Valid values are: ' . implode(', ', $validDirections),
                'data' => []
            ], 400);
        }
        // $meetings = Meeting::where('organization_id', $org_id)->with('groups')->get();
        $meetings = Meeting::query()
            ->where('organization_id', $org_id);

        try {
            // Check for conflicting query variables
            if ($historic && $active) {
                return response()->json([
                    'status' => 422, // Unprocessable Entity
                    'org' => $org,
                    'message' => 'Unsupported request, check query variables.',
                    'data' => []
                ], 422);
            }

            // Apply filtering for historic or active
            if ($historic) {
                $meetings->where('meeting_date', '<', Carbon::parse($historic));
            } elseif ($active) {
                $meetings->where('meeting_date', '>=', Carbon::parse($active));
            }

            // Set default direction based on query variables
            // if (!$direction) {
            //     $direction = ($historic) ? 'desc' : 'asc'; // Default to desc for historic, asc for active
            // }
            // Validate direction parameter
            if (!in_array(strtolower($direction), $validDirections)) {
                return response()->json([
                    'status' => 400, // Bad Request
                    'active' => $active,
                    'historic' => $historic,
                    'org' => $org,
                    'direction' => $direction,
                    'message' => 'Invalid direction. Valid values are: ' . implode(', ', $validDirections),
                    'data' => []
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 400, // Bad Request
                'org' => $org,
                'historic' => $historic,
                'active' => $active,
                'direction' => $direction,
                'message' => 'Invalid before date format. Use YYYY-MM-DD format.',
                'data' => []
            ], 400);
        }

        // Apply sorting based on direction
        $meetings->orderBy('meeting_date', strtoupper($direction));

        // Apply pagination with user-specified page number if available
        $page = $request->query('page', 1); // Default to page 1
        $meetings = $meetings->paginate(perPage: $perPage, page: $page);
        // Load groups for each meeting (after pagination)
        $meetings->each(function ($meeting) {
            $meeting->load('groups');
        });

        if ($meetings->count() > 0) {
            return response()->json([
                'status' => 200,
                'org' => $org,
                'historic' => $historic,
                'active' => $active,
                'direction' => $direction,
                'data' => $meetings->toArray(), // Convert paginated collection to array
                'pagination' => [
                    'current_page' => $meetings->currentPage(),
                    'total_pages' => $meetings->lastPage(),
                    'per_page' => $perPage,
                    'total' => $meetings->total(),
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 404, // Consider 204 No Content if appropriate
                'org' => $org,
                'message' => 'No meetings found for this organization and before date.',
                'data' => []
            ], 404);
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
            return response()->json(['status' => 422, 'message' => 'POST request failed', 'errors' => $validator->errors()], 422);
        }

        /* Check if organization_id exists in the organizations table */
        $organizationId = $request->input('organization_id');
        if (!Organization::where('id', $organizationId)->exists()) {
            return response()->json(['status' => 422, 'message' => 'Invalid organization_id'], 422);
        }

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate UUID
        $meeting = new Meeting($request->all());
        $meeting->id = $uuid; // Set UUID as id
        $meeting->save();

        return response()->json(['status' => 200, 'message' => 'New meeting successful', 'data' => $meeting], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $org, string $id)
    {
        // Get the meeting
        $meeting = Meeting::where('organization_id', $org)
            ->where('id', $id)
            ->first();

        // Check if the meeting exists
        if (!$meeting) {
            return response()->json(['status' => 404, 'data' => [], 'message' => 'Meeting not found'], 404);
        }

        // Check if organization_id is provided in the request
        if ($request->has('organization_id')) {
            $organizationId = $request->input('organization_id');
            if (!Organization::where('id', $organizationId)->exists()) {
                return response()->json(['status' => 494, 'data' => [], 'message' => 'Invalid request. Organization ID does not exist'], 422);
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
            return response()->json(['status' => 422, 'data' => [], 'message' => 'Update failed', 'errors' => $validator->errors()], 422);
        }

        // Check if donation key exists and format it if needed
        if ($request->has('donation')) {
            $request->merge(['donation' => number_format($request->input('donation'), 2)]);
        }

        // Update values
        if ($meeting->update($request->all())) {
            return response()->json(['status' => 200, 'message' => 'Update successful', 'data' => $meeting], 200);
        } else {
            return response()->json(['status' => 422, 'message' => 'Update failed', 'data' => []], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $org, string $id)
    {
        // Delete meeting where organization_id matches $org and id matches $id
        $meetingDeleted = Meeting::where('organization_id', $org)
            ->where('id', $id)
            ->delete();

        if ($meetingDeleted > 0) {
            // Meeting deleted successfully - return 200 with message
            return response()->json(['status' => 200, 'message' => 'Meeting deleted successfully'], 200);
        } else {
            // Meeting not found or deletion failed - return 404 with message
            return response()->json(['status' => 404, 'message' => 'Meeting not found'], 404);
        }
    }

    public function getOrgMeeting(Request $request, string $org, string $meeting)
    {
        //{{baseURL}}/meeting/$org/$meeting
        // Access route parameters directly
        $result = Meeting::where('organization_id', $org)
            ->where('id', $meeting)
            ->first();

        if ($result) {
            // Retrieve all groups related to the meeting
            $groups = Group::where('meeting_id', $meeting)->get();

            // Add the groups to the meeting object
            $result->groups = $groups;

            return response()->json(['status' => 200, 'data' => $result], 200);
        } else {
            return response()->json([
                'status' => 404,
                'org' => $org,
                'meeting' => $meeting,
                'message' => 'Meeting not found'
            ], 404);
        }
    }
}
