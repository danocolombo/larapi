<?php

namespace App\Http\Controllers;

use App\Models\Affiliation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Import Str class for UUID generation
use Illuminate\Http\Request;

class AffiliationController extends Controller
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
        $affiliations = Affiliation::paginate(15, ['*'], 'page', $page);

        return $affiliations;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* Validate POST request body */
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'status' => 'required',
            'person_id' => 'required|UUID',
            'organization_id' => 'required|UUID'
        ]);

        /* Check for validation errors */
        if ($validator->fails()) {
            return response()->json(['message' => 'POST request failed', 'request' => $request->all()], 422);
        }

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $affiliation = new Affiliation($request->all());
        $affiliation->id = $uuid; // Set UUID as id
        $affiliation->save();

        return response()->json(['message' => 'New affiliation successful', 'affiliation' => $affiliation], 200);
    }
    public function update(Request $request, string $id)
    {
        // Validate the request body
        $validator = Validator::make($request->all(), [
            'role' => 'required_without_all:status',
            'status' => 'required_without_all:role',
        ]);

        // Validate the $id parameter
        $idValidationRules = [
            'id' => 'required|uuid|exists:affiliations,id',
        ];

        $request->merge(['id' => $id]);
        $request->validate($idValidationRules);

        // Get the jericho_user to update
        $affiliation = Affiliation::find($id);

        // If the affiliation doesn't exist, return 404
        if (!$affiliation) {
            return response()->json(['message' => 'affiliation not found'], 404);
        }

        // Update values
        if ($affiliation->update($request->all())) {
            return response()->json(['message' => 'Update successful', 'affiliation' => $affiliation], 200);
        } else {
            return response()->json(['message' => 'Update failed'], 422);
        }
    }
    public function getAffiliation(string $id)
    {
        $affiliation = Affiliation::find($id);

        if ($affiliation) {
            // Record found, return JSON with "data" key and record object
            return response()->json([
                'data' => $affiliation
            ], 200);
        } else {
            // Record not found, return 404 with "data" key set to null
            return response()->json([
                'data' => null
            ], 404);
        }
    }
    public function getAffiliationsForPerson(string $person, Request $request, int $page = 1)
    {
        // Check if page is provided in the request (query parameter or path variable)
        if ($request->has('page')) {
            $page = $request->query('page');
        }

        // Fetch paginated affiliations for the specified person
        $affiliations = Affiliation::where('person_id', '=', $person)
            ->paginate(15, ['*'], 'page', $page);

        if ($affiliations->count() > 0) {
            // Records found, return 200 with "data" key and full pagination links
            return response()->json([
                'data' => $affiliations->items(),
                'total' => $affiliations->total(),
                'page' => $page,
                'last_page' => $affiliations->lastPage(),
                'next_page_url' => $affiliations->hasMorePages() ? $baseUrl . $affiliations->nextPageUrl() : null,
                'prev_page_url' => $affiliations->previousPageUrl() ? $baseUrl . $affiliations->previousPageUrl() : null,
                // Add other links like 'first_page_url', 'last_page_url' (optional)
            ], 200);
        } else {
            // Records not found, return 404 with "data" key set to null
            return response()->json([
                'data' => null
            ], 404);
        }
    }
    public function getAffiliationForOrganization(string $organization)
    {

        $affiliations = Affiliation::where('organization_id', '=', $organization)->get();
        if ($affiliations->count() > 0) {
            // Records found, return 200 with "data" key and affiliation objects
            return response()->json([
                'data' => $affiliations
            ], 200);
        } else {
            // Records not found, return 404 with "data" key set to null
            return response()->json([
                'data' => null
            ], 404);
        }
    }
    public function destroy(string $id)
    {
        // Validate that $id is provided and is a valid UUID
        $validationRules = [
            'id' => 'required|uuid|exists:affiliations,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (Affiliation::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['message' => 'Destroy Affiliation successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['message' => 'Destroy Affiliation unsuccessful'], 422);
        }
    }
}
