<?php

namespace App\Http\Controllers;

use App\Models\Affiliation;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import Str class for UUID generation

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Person::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* Validate POST request body */
        $validator = Validator::make($request->all(), [
            'sub' => 'required|uuid', // Ensure sub is a valid UUID
            'username' => 'required',
            'default_org_id' => 'nullable|uuid', // Allow null or a valid UUID
            'location_id' => 'nullable|uuid', // Allow null or a valid UUID
        ]);

        /* Check for validation errors */
        if ($validator->fails()) {
            return response()->json(['message' => 'POST request failed', 'request' => $request->all()], 422);
        }
        // Check if the 'sub' value already exists in the table
        $existingPerson = Person::where('sub', $request->sub)->first();
        if ($existingPerson) {
            return response()->json(['message' => 'Duplicate sub value', 'sub' => $existingPerson->sub], 422);
        }
        // Check if the 'sub' value already exists in the table
        $existingPerson = Person::where('username', $request->username)->first();
        if ($existingPerson) {
            return response()->json(['message' => 'Username is already used', 'username' => $existingPerson->username], 422);
        }
        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $person = new Person($request->all());
        $person->id = $uuid; // Set UUID as id
        $person->save();

        return response()->json(['message' => 'New person successful', 'person' => $person], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request body
        $validator = Validator::make($request->all(), [
            'default_org_id' => 'nullable|uuid', // Allow null or a valid UUID
            'location_id' => 'nullable|uuid', // Allow null or a valid UUID
        ]);

        // Validate the $id parameter
        $idValidationRules = [
            'id' => 'required|uuid|exists:persons,id',
        ];

        $request->merge(['id' => $id]);

        $request->validate($idValidationRules);

        // If location_id is provided, validate it is UUID
        if ($request->has('location_id')) {
            $validator->sometimes('location_id', 'uuid', function ($input) {
                return $input->location_id !== null;
            });
        }

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['message' => 'Update failed', 'errors' => $validator->errors()], 422);
        }

        // Get the jericho_user to update
        $person = Person::find($id);

        // If the obligation doesn't exist, return 404
        if (!$person) {
            return response()->json(['message' => 'Person not found'], 404);
        }

        // Update values
        if ($person->update($request->all())) {
            return response()->json(['message' => 'Update successful', 'person' => $person], 200);
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
            'id' => 'required|uuid|exists:persons,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (Person::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['message' => 'Destroy Person successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['message' => 'Destroy Person unsuccessful'], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Person::find($id);
    }

    /**
     * Display the specified resource.
     */
    public function getSub(string $id)
    {
        // Get the person based on the 'sub' identifier
        // $person = Person::with(['affiliations:id,name,person_id', 'organization'])->where('sub', $id)->first();
        // $person = Person::with(['affiliations' => function ($query) use ($id) {
        //     $query->where('person_id', $id);
        // }, 'organization:id,default_org_id' => function ($query, $person) {
        //     // Access the 'person' object passed as a second argument
        //     $query->where('id', $person->default_org_id);
        // }])
        //     ->where('sub', $id)
        //     ->first();
        $person = Person::with([
            'affiliations' => function ($query) use ($id) {
                $query->where('person_id', $id);
            },
            'defaultOrg' // No need for an inner closure here
        ])
            ->where('sub', $id)
            ->first();
        // Check if the person exists
        if (!$person) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the person has a default organization
        // if (!$person->organization) {
        //     return response()->json(['message' => 'Default organization not defined'], 400);
        // }

        // Return the person data with affiliations and organization included
        return response()->json(['data' => $person], 200);
    }
    public function getSub1(string $id)
    {
        // Get the person based on the 'sub' identifier
        $person = Person::where('sub', $id)->first();

        // Check if the person exists
        if (!$person) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the person has a default organization
        if (!$person->default_org_id) {
            return response()->json(['message' => 'Default organization not defined'], 400);
        }

        // Get affiliations for the person with the default organization
        $affiliations = Affiliation::where('person_id', $person->id)
            ->where('organization_id', $person->default_org_id)
            ->get();

        // Get the default organization
        $organization = Organization::find($person->default_org_id);

        // Add affiliations and default organization to the person object
        // $person->affiliations->items =  $affiliations;
        // $person->current_org = $organization;

        // Return the person data with HTTP status code 200 (OK)
        return response()->json(['data' => $person], 200);
    }




    public function search(string $name)
    {
        /**
         * the second parameter is the sql command and we concatenate
         *  % on the front and back of the input variable
         */
        return Person::where('username', 'like', '%' . $name . '%')->get();
    }
}
