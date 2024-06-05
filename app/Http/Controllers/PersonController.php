<?php

namespace App\Http\Controllers;

use App\Models\Affiliation;
use App\Models\Location;
use App\Models\Person;
use Illuminate\Support\Facades\Storage; // Import the Storage facade
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import Str class for UUID generation

class PersonController extends Controller
{
    public function download($filePath)
    {
        // Check if the file exists
        if (!Storage::exists($filePath)) {
            return null; // Or handle the error as needed
        }

        // Return the file content or a download response
        return Storage::get($filePath);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, int $page = 1)
    {
        // Check if page is provided in the request (query parameter)
        $pageSize = 10;
        if ($request->has('page')) {
            $page = $request->query('page');
        }

        // Fetch paginated affiliations based on requested page
        $people = Person::paginate($pageSize, ['*'], 'page', $page);

        return $people;
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
            return response()->json(['status' => 404, 'message' => 'Person not found'], 404);
        }

        // Update values
        if ($person->update($request->all())) {
            return response()->json(['status' => 200, 'message' => 'Update successful', 'person' => $person], 200);
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
            'id' => 'required|uuid|exists:persons,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (Person::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['status' => 200, 'message' => 'Destroy Person successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['status' => 422, 'message' => 'Destroy Person unsuccessful'], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $person =  Person::find($id);
        $person = Person::with('affiliations', 'defaultOrg', 'location')->find($id);

        if (!$person) {
            return response()->json(['status' => 404, 'data' => [], 'message' => 'Not found'], 404);
        } else {
            return response()->json(['status' => 200, 'data' => $person], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function getPersonBySub(string $sub)
    {
        $person = Person::with('affiliations', 'location', 'defaultOrg')
            ->where('sub', $sub)
            ->first();

        if (!$person) {
            return response()->json(['status' => 404, 'data' => [], 'message' => 'User not found'], 404);
        }

        $affiliations = $person->affiliations->all(); // Assuming affiliations is a collection
        $location = $person->location ? $person->location->first() : null; // If location is a model

        $person->affiliations = $affiliations;
        $person->location = $location;

        // Return the person data directly
        return response()->json(['status' => 200, 'data' => $person], 200);
    }

    public function getPersonBySubEXTRA(string $sub)
    {
        $person = Person::with('affiliations', 'location')
            ->where('sub', $sub)
            ->first();

        if (!$person) {
            return response()->json(['status' => 404, 'data' => [], 'message' => 'User not found'], 404);
        }

        $affiliations = $person->affiliations->all(); // Assuming affiliations is a collection
        $location = $person->location ? $person->location->first() : null; // If location is a model

        $person->data = [ // Consider a dedicated key for nested data
            'affiliations' => $affiliations,
            'location' => $location,
        ];

        // Return the person data with affiliations and organization included
        return response()->json(['status' => 200, 'data' => $person], 200);
    }


    public function search(Request $request)
    {
        /**
         * the second parameter is the sql command and we concatenate
         *  % on the front and back of the input variable
         */
        $perPage = 10; // Meetings per page
        $page = $request->query('page');
        $sub = $request->query('sub');
        $username = $request->query('username');
        $email = $request->query('email');

        if (!$sub && !$username && !$email) {
            return response()->json(['status' => 422, 'data' => [], 'message' => 'No search criteria provided'], 422);
        }
        $people = null; //
        if ($sub && $username) {
            return response()->json(['status' => 422, 'data' => [], 'message' => 'Unsupported criteria provided'], 422);
        }
        if ($sub && $email) {
            return response()->json(['status' => 422, 'data' => [], 'message' => 'Unsupported criteria provided'], 422);
        }
        if ($username && $email) {
            return response()->json(['status' => 422, 'data' => [], 'message' => 'Unsupported criteria provided'], 422);
        }
        if ($sub) {
            $people = Person::where('sub', 'like', '%' . $sub . '%')->paginate(perPage: 10);
        } elseif ($username) {
            $people = Person::where('username', 'like', '%' . $username . '%')->paginate(perPage: 10);
        } elseif ($email) {
            $people = Person::where('email', 'like', '%' . $email . '%')->paginate(perPage: 10);
        }
        if ($people->count() > 0) {
            return response()->json([
                'status' => 200,
                'data' => $people->toArray(), // Convert paginated collection to array
                'pagination' => [
                    'current_page' => $people->currentPage(),
                    'total_pages' => $people->lastPage(),
                    'per_page' => $perPage,
                    'total' => $people->total(),
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 404, // Consider 204 No Content if appropriate
                'message' => 'Nothing found',
                'data' => []
            ], 404);
        }
    }
    /**
     * upload a profile picture
     */
    public function uploadProfilePicture(Request $req, string $id)
    {
        // $path = 'public/profile_pics/' . $id;
        // $result = $req->file('xyz')->store($path);
        // return ["results" => $result];

        // $path = '/profile_pics/' . $id;
        // $result = $req->file('xyz')->store($path, 'public'); // Specify the 'public' disk
        // return ["results" => $result];

        $path = '/profile_pics/' . $id; // Base path for subdirectory
        $fileName = $req->file('image-file')->store($path, 'public'); // Save the file with a generated name

        // Construct the full path with the uploaded filename
        $fullPath = "$path/$fileName";

        return ["results" => $fullPath]; // Return the full path of the uploaded file

    }

    /**
     *    Download a profile picture
     **/
    public function downloadProfilePicture(string $id, string $fileName)
    {
        if (!$id || !$fileName) {
            // Missing ID or filename, return 400 error
            return response()->json([
                'status' => 400,
                'message' => 'Invalid request: Missing ID or filename parameter',
            ]);
        }

        $path = 'profile_pics/' . $id . '/' . $fileName;

        if (!Storage::disk('local')->exists($path)) {
            // File not found, return 404 error
            return response()->json([
                'status' => 404,
                'message' => 'Profile picture not found',
            ]);
        }

        // Download the file using Storage facade
        return Storage::disk('local')->download($path);
    }

    /** 
     * List the files for the user
     */
    public function getProfilePictureList(string $id)
    {
        if (!$id) {
            // No ID provided, return 422 error
            return response()->json([
                'status' => 422,
                'message' => 'Invalid request: Missing ID parameter',
                'files' => [],
            ]);
        }

        $path = 'profile_pics/' . $id;

        // Use Storage facade to get all files from the directory
        $files = Storage::disk('local')->files($path);

        // Extract filenames and potentially subdirectories
        $fileList = [];
        foreach ($files as $item) {
            // Check if it's a file, add filename to the list
            if (Storage::disk('local')->exists($item)) {
                $fileList[] = basename($item);
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Profile pictures retrieved successfully',
            'files' => $fileList,
        ]);
    }
}
