<?php

namespace App\Http\Controllers;

use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class UserController extends Controller
{
    public function getUserDetails()
    {
        try {
            $user = Auth::user();

            if ($user) {
                return response()->json([
                    'name' => $user->name,
                    'email' => $user->email,
                ]);
            }

            return response()->json(['message' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch user'], 500);
        }
    }
    
    public function saveUserPreferences(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Validate the request data
            $request->validate([
                'NewsAPI' => 'required|boolean',
                'NewsAPICategories' => 'array',
                'NewsAPICategories.*.key' => 'required|string',
                'NewsAPICategories.*.name' => 'required|string',
                'NyTimes' => 'required|boolean',
                'NyTimesCategories' => 'array',
                'NyTimesCategories.*.key' => 'required|string',
                'NyTimesCategories.*.name' => 'required|string',
                'Guardian' => 'required|boolean',
                'GuardianCategories' => 'array',
                'GuardianCategories.*.key' => 'required|string',
                'GuardianCategories.*.name' => 'required|string',
            ]);

            // Retrieve the preference data from the request
            $preferenceData = $request->json()->all();

            // Save the preferences to the database
            $preference = Preference::updateOrCreate(
                ['user_id' => $user->id],
                ['preference_data' => json_encode($preferenceData)]
            );

            return response()->json(['message' => 'Preferences saved successfully']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save preferences'], 500);
        }
    }

    public function getUserPreferences()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $preference = $user->preference;

            if (!$preference) {
                return response()->json(['message' => 'Preferences not found'], 404);
            }

            $preferenceData = json_decode($preference->preference_data, true);

            return response()->json($preferenceData);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch preferences'], 500);
        }
    }

}
