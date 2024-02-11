<?php

namespace App\Http\Controllers;

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

    public function search(string $name)
    {
        /**
         * the second parameter is the sql command and we concatenate
         *  % on the front and back of the input variable
         */
        return Person::where('username', 'like', '%' . $name . '%')->get();
    }
}
