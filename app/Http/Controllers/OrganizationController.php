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
        $organizations = Organization::query()->paginate(perPage: 10);
        return response()->json(['status' => 200, 'data' => $organizations], 200);
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
            return response()->json(['status' => 404, 'data' => [], 'message' => 'Organization not found'], 404);
        }

        // Update values
        if ($organization->update($request->all())) {
            return response()->json(['status' => 200, 'data' => $organization, 'message' => 'Update successful'], 200);
        } else {
            return response()->json(['status' => 422, 'data' => [], 'message' => 'Update failed'], 422);
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
    public function getOrganizationById(string $id)
    {
        $organization =  Organization::find($id);
        if ($organization) {
            // Record found, return JSON with "data" key and record object
            return response()->json([
                'status' => 200,
                'data' => $organization
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

    public function search(Request $request)
    {
        /**
         * the second parameter is the sql command and we concatenate
         *  % on the front and back of the input variable
         */
        $perPage = 10; // Meetings per page
        $code = $request->query('code');
        $name = $request->query('name');
        if (!$code && !$name) {
            return response()->json(['status' => 422, 'data' => [], 'message' => 'No search criteria provided'], 422);
        }
        $organizatoins = null; //
        if ($code && $name) {
            return response()->json(['status' => 422, 'data' => [], 'message' => 'Unsupported criteria provided'], 422);
        }
        if ($code) {
            $organizations = Organization::where('code', 'like', '%' . $code . '%')->paginate(perPage: 10);
        } elseif ($name) {
            $organizations = Organization::where('name', 'like', '%' . $name . '%')->paginate(perPage: 10);
        }
        // $organizations = Organization::where('code', 'like', '%' . $code . '%')->paginate(perPage: 10);
        if ($organizations->count() > 0) {
            return response()->json([
                'status' => 200,
                'data' => $organizations->toArray(), // Convert paginated collection to array
                'pagination' => [
                    'current_page' => $organizations->currentPage(),
                    'total_pages' => $organizations->lastPage(),
                    'per_page' => $perPage,
                    'total' => $organizations->total(),
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 404, // Consider 204 No Content if appropriate
                'name' => $name,
                'message' => 'Not found',
                'data' => []
            ], 404);
        }

        return response()->json(['status' => 200, 'data' => $organizations], 200);
    }
}
