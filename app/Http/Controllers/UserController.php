<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            // Custom error messages
            $customMessages = [
                'name.required' => 'Please provide a name.',
                'email.required' => 'Please provide an email address.',
                'email.email' => 'Please provide a valid email address.',
                'email.unique' => 'The email address has already been taken.',
                'password.required' => 'Please provide a password.',
                'password.min' => 'The password must be at least 6 characters long.',
            ];

            // Replace default error messages with custom messages
            $validator->setCustomMessages($customMessages);

            return response()->json([
                'data' =>  $validator->errors(),
                'message' => 'Validation failed',
                'status' => false
            ], 422);
        }
        // Validation passed, create the user
        $user = User::create([
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'email' => $request->email
        ]);

        $tokenResult = $user->createToken('API Token');
        $token = $tokenResult->accessToken;

        return response()->json([
            'data' => ['token' => $token],
            'message' => 'User registered successfully',
            'status' => true
        ], 200);
    }
}
