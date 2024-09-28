<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class UserLoginController extends Controller
{
    
    public function register(Request $request)
    {
         // Validate the incoming request data.
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        // If validation fails, return a 422 response with the validation errors.
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        // Create a new user in the database.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => isset($request->role)?$request->role:'user',
            'password' => Hash::make($request->password),
        ]);
        // Return a 201 response indicating the user was registered successfully.
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }
    public function login(Request $request)
    {
        // Validate the incoming request data.
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        // If validation fails, return a 422 response with the validation errors.
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Attempt to generate a JWT token using the provided email and password.
            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json(['success' => false, 'error' => 'Invalid credentials'], 401); // Unauthorized
            }
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Could not create token'], 500); // Internal Server Error
        }
        // Return a 200 response containing the generated token.
        return response()->json([
            'success' => true,
            'token' => $token,
        ], 200);
    }
    public function refreshToken(Request $request)
    {
        try {
            // Refresh the JWT token.
            $token = JWTAuth::refresh(JWTAuth::getToken());
            // Return a 200 response containing the new token.
            return response()->json([
                'success' => true,
                'token' => $token,
            ], 200);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Could not refresh token'], 500); // Internal Server Error
        }
    }
    public function logout()
    {
        // Invalidate the current JWT token.
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'User logged out successfully']);
    }
}
