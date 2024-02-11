<?php

namespace App\Http\Controllers;

use App\Models\Affiliation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Import Str class for UUID generation
use Illuminate\Http\Request;

class AffiliationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Affiliation::all();
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
    public function show(string $id)
    {
        return Affiliation::find($id);
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
    // public function target(Request $request)
    // {
    //     Log::info('Received request:', $request->all());
    //     return response()->json(['message' => 'filter not identified', 'request' => $request], 422);
    //     // Your existing logic here...

    //     // Log the response or return statements if needed...

    //     // Return response...
    // }
    // public function search2(Request $request)
    // {
    //     $query = Affiliation::query();

    //     if ($request->has('person_id')) {
    //         $personId = $request->input('person_id');
    //         // $query->where('person_id', $personId);
    //         return response()->json(['message' => 'person_id identified', 'person_id' => $personId], 200);
    //     } elseif ($request->has('organization_id')) {
    //         $organizationId = $request->input('organization_id');
    //         // $query->where('organization_id', $organizationId);
    //         return response()->json(['message' => 'organization_id identified', 'organization_id' => $organizationId], 200);
    //     } else {
    //         return response()->json(['message' => 'filter not identified', 'request' => $request], 422);
    //     }

    //     // $results = $query->get();

    //     // return response()->json($results);
    // }
}
