<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Register
    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|min:6'
        ];

        $validator = Validator::make($request->input(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validator->errors()->all()
                ],
                400
            );
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(
            [
                'status' => true,
                'message' => 'User created successfully',
                'token' => $user->createToken('API-TOKEN')->plainTextToken
            ],
            200
        );
    }

    // Login

    public function login(Request $request)
    {
        // $rules = [
        //     'email' => 'required|string|email|max:100',
        //     'password' => 'required|string'
        // ];

        // $validator = Validator::make($request->input(), $rules);

        // if ($validator->fails()) {
        //     return response()->json(
        //         [
        //             'status' => false,
        //             'errors' => $validator->errors()->all()
        //         ],
        //         400
        //     );
        // }

        // if (!Auth::attempt($request->only('email', 'password'))) {
        //     return response()->json(
        //         [
        //             'status' => false,
        //             'errors' => ['Unauthorized']
        //         ],
        //         401
        //     );
        // }
        $user = User::where('email', $request->email)->first();
        $userWithToken = $user->toArray();
        $userWithToken['token'] = $user->createToken('API-TOKEN')->plainTextToken;

        return response()->json(
            [
                'status' => true,
                'message' => 'User logged in successfully',
                'data' => $userWithToken

            ],
            200
        );
    }

    // Verify Credencial


    public function verifyCredentials(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:100',
            'password' => 'required|string'
        ];

        $validator = Validator::make($request->input(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validator->errors()->all()
                ],
                400
            );
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(
                [
                    'status' => false,
                    'errors' => ['Invalid credentials']
                ],
                401
            );
        }


        return response()->json(
            [
                'status' => true,
                'message' => 'User logged in successfully',


            ],
            200
        );
    }


    // Logout

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(
            [
                'status' => true,
                'message' => 'Token deleted successfully'
            ],
            200
        );
    }
}
