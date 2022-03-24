<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {

        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                 'name' => 'required|max:255',
                 'email' => 'required|unique:users',
                 'password' => 'required|confirmed',
              ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()]);
            }
        	$registerData = $request->only(['name','email']);
        	$registerData['password'] = Hash::make($request->password);
            $user = User::create($registerData);
            $accessToken = $user->createToken('authToken')->accessToken;
            $output = ['user' => $user, 'access_token' => $accessToken];
            DB::commit();
        } catch (\Exception $e) {
            $output = ['msg' => $e->getMessage()];
            DB::rollBack();
        }
        return response()->json($output);
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
