<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;


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

}
