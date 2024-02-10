<?php

namespace App\Http\Controllers;

use App\Models\JerichoUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import Str class for UUID generation

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return JerichoUser::all();
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

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $theUser = new JerichoUser($request->all());
        $theUser->id = $uuid; // Set UUID as id
        $theUser->save();

        return response()->json(['message' => 'New user successful', 'user' => $theUser], 200);
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
            'id' => 'required|uuid|exists:jericho_users,id',
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
        $the_user = JerichoUser::find($id);

        // If the obligation doesn't exist, return 404
        if (!$the_user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update values
        if ($the_user->update($request->all())) {
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
            'id' => 'required|uuid|exists:jericho_users,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (JerichoUser::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['message' => 'Destroy User successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['message' => 'Destroy User unsuccessful'], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return JerichoUser::find($id);
    }

    public function search(string $name)
    {
        /**
         * the second parameter is the sql command and we concatenate
         *  % on the front and back of the input variable
         */
        return JerichoUser::where('username', 'like', '%' . $name . '%')->get();
    }
}
