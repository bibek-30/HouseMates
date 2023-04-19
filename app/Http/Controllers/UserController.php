<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    //get all users
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    // get singel user
    public function singleUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(["message" => "User not found"], 404);
        }

        return response()->json($user, 200);
    }

    // Registeration of the user
    public function create(Request $request)
    {
        // return $request->role;
        $request->validate([
            'name' => 'required',
            'gender' => 'in:male,female,others',
            'mobile_number' => 'required|regex:/9[6-8]{1}[0-9]{8}/',
            'email' => 'required | email|unique:users',
            'role' => 'in:admin,user',
            'password' => 'required|min:8',
            'confirm_password' => 'required_with:password|same:password',
        ]);

        $user = User::create([
            'name' => $request->name,
            'gender' => $request->gender,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);


        $token = $user->createToken($request->email)->plainTextToken;

        $response = [
            "status"  => 200,
            "message" => "User Account Created Successfully",
            "user" => $user,
            // "role" => $user->role,
            "token" => $token,
        ];
        // try {

        // Response if user created successfully
        return response()->json($response, 200);
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
        // return $token;

        $response = [
            "status" => 200,
            "user"  => $user,
            "token" => $token,
            "message" => "Loged in Sucessfully."

        ];


        return response()->json($response, 200);
    }


    public function countUsers()
    {
        $userCount = User::count();
        return $userCount;
    }

    //delete user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["message" => "User not found"], 404);
        }
        $user->delete();
        $successResponse = ["message" => "User deleted successfully"];
        return response()->json($successResponse, 200);
    }

    // Change Password
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password'     => 'required|min:8|max:20',
        ]);
        // regex:/((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,20})/

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation fails',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'Password is successfully updated.',
            ], 201);
        } else {
            return response()->json([
                'message' => 'Old password does not matched!',
            ], 400);
        }
    }
}
