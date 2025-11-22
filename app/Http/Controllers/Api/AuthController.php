<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register()
    {
//        $member = Member::create([
//            'firstname' => $request->name,
//            'email' => $request->email,
//            'phone' => $request->phone,
//            'password' => Hash::make($request->password),
//        ]);

//        $token = $member->createToken('auth-token')->plainTextToken;
//
//        return response()->json([
//            'member' => $member,
//            'token' => $token,
//        ], 201);
    }

    public function login(LoginRequest $request){
        $request->validated($request->all());

        if (!Auth::guard('member')->attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $member = Auth::guard('member')->user();
        $token = $member->createToken('auth-token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }
}
