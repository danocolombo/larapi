<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Import Str class for UUID generation
class SystemSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SystemSetting::all();
    }
    public function store(Request $request)
    {
        /* Validate POST request body */
        $validator = Validator::make($request->all(), [
            'app_name' => 'required_without_all:android_version,ios_version,web_version,default_profile_picture,logo_picture',
            'android_version' => 'required_without_all:app_name,ios_version,web_version,default_profile_picture,logo_picture',
            'ios_version' => 'required_without_all:app_name,android_version,web_version,default_profile_picture,logo_picture',
            'web_version' => 'required_without_all:app_name,android_version,ios_version,default_profile_picture,logo_picture',
            'default_profile_picture' => 'required_without_all:app_name,android_version,ios_version,web_version,logo_picture',
            'logo_picture' => 'required_without_all:app_name,android_version,ios_version,web_version,default_profile_picture',
        ]);

        /* Check for validation errors */
        if ($validator->fails()) {
            return response()->json(['message' => 'POST request failed', 'errors' => $validator->errors()], 422);
        }

        /* Generate UUID for the id field */
        $uuid = Str::uuid()->toString(); // Generate 
        $sysSetting = new SystemSetting($request->all());
        $sysSetting->id = $uuid; // Set UUID as id
        $sysSetting->save();

        return response()->json(['message' => 'New system setting successful', 'sysSetting' => $sysSetting], 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Get the default group
        $sysSetting = SystemSetting::find($id);

        // Check if the default group exists
        if (!$sysSetting) {
            return response()->json(['message' => 'Settings not found'], 404);
        }

        // Validate the request body
        $validator = Validator::make($request->all(), [
            'app_name' => 'required_without_all:android_version,ios_version,web_version,default_profile_picture,logo_picture',
            'android_version' => 'required_without_all:app_name,ios_version,web_version,default_profile_picture,logo_picture',
            'ios_version' => 'required_without_all:app_name,android_version,web_version,default_profile_picture,logo_picture',
            'web_version' => 'required_without_all:app_name,android_version,ios_version,default_profile_picture,logo_picture',
            'default_profile_picture' => 'required_without_all:app_name,android_version,ios_version,web_version,logo_picture',
            'logo_picture' => 'required_without_all:app_name,android_version,ios_version,web_version,default_profile_picture',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // return response()->json(['message' => 'Update failed', 'errors' => $validator->errors()], 422);
            return response()->json(['message' => 'Update failed, check the put body'], 422);
        }

        // Extract only the specified values from the request
        $data = $request->only(['app_name', 'android_version', 'ios_version', 'web_version', 'default_profile_picture', 'logo_picture']);

        // Update values
        if ($sysSetting->update($data)) {
            return response()->json(['message' => 'Update successful', 'system settings' => $sysSetting], 200);
        } else {
            return response()->json(['message' => 'Update failed'], 422);
        }
    }
    public function show(string $id)
    {
        return SystemSetting::find($id);
    }
    public function destroy(string $id)
    {
        // Validate that $id is provided and is a valid UUID
        $validationRules = [
            'id' => 'required|uuid|exists:system_settings,id',
        ];

        // Merge the ID into the request for validation
        $request = request()->merge(['id' => $id]);

        // Validate the request
        $request->validate($validationRules);

        // Attempt to delete the account
        if (SystemSetting::destroy($id)) {
            // If successful, return a 200 response with the message
            return response()->json(['message' => 'Destroy setting successful'], 200);
        } else {
            // If unsuccessful, return a 422 response with the message
            return response()->json(['message' => 'Destroy setting unsuccessful'], 422);
        }
    }
}
