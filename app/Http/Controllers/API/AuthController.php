<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
    	$registerData = $request->validate([
    		'name' => 'required|max:255',
    		'email' => 'required|unique:users',
    		'password' => 'required|confirmed',
    	]);
    	$registerData['password'] = Hash::make($request->password);
    	$user = User::create($registerData);
    	$accessToken = $user->createToken('authToken')->accessToken;
    	return response(['user' => $user, 'access_token' => $accessToken], 201);
    }


    public function login(Request $request) {
    	$login = $request->validate([
    		'email' => 'email|required',
    		'password' => 'required',
    	]);

    	if (!auth()->attempt($login)) {
			return response(['message' => 'Invalid Login Details.'], 400);    		
    	}

    	$accessToken = auth()->user()->createToken('authToken')->accessToken;

    	return response(['user' => auth()->user(), 'access_token' => $accessToken], 201);
    }
}
