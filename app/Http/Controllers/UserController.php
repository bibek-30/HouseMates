<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    //get all users
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    // Registeration of the user
    public function create(Request $request)
    {
        // return;
        $request->validate([
            'name' => 'required',
            'gender' => 'in:male,female,others',
            'mobile_number' => 'required|regex:/9[6-8]{1}[0-9]{8}/',
            'email' => 'required | email|unique:users',
            'password' => 'required|min:8',
            'confirm_password' => 'required_with:password|same:password',
        ]);


        $user = User::create([
            'name' => $request->name,
            'gender' => $request->gender,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken($request->email)->plainTextToken;

        $response = [
            "status"  => 200,
            "message" => "User Account Created Successfully",
            "user" => $user,
            "token" => $token
        ];
        try {

            // Response if user created successfully
            return response()->json($response, 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(["error" => "An error occurred while processing your request"], 500);
        }
    }


    //Log in
    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(["message" => "Invalid username and password provided"], 404);
        }

        $token = $user->createToken($request->email)->plainTextToken;

        $response = [
            "user"  => $user,
            "status" => 200,
        ];

        return response()->json($response, 200);
    }
}
