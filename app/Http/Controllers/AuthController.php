<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(StoreUserRequest $request)
    {
        try {
            $user = User::create($request->validated());

            $data['token'] =  $user->createToken('NewsApp')->plainTextToken;
            $data['user'] =  new UserResource($user);
            return response()->json($data, 201);

        } catch(QueryException $e){
            //db constaints may fail

            $errorCode = $e->errorInfo[1];
            $data = [
                'message' => 'Registration failed.',
                'errors' => [
                    'message' =>  "Error creating record. Try again later. (ErrorCode: {$errorCode})",
                 ]
            ];

            return response()->json($data, 400);
        }

    }

    /**
     * Generate a token for a user.
     *
     * @param  \App\Http\Requests\LoginUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginUserRequest $request)
    {
        if(Auth::attempt($request->validated())){
            $user = Auth::user();

            // Revoke all previous tokens...
            $user->tokens()->delete();

            $data['token'] =  $user->createToken('NewsApp')->plainTextToken;
            $data['user'] =  new UserResource($user);
            return response()->json($data, 200);
        }

       $data = [
            'message' => 'Incorrect email/password.',
            'errors' => [
                'message' =>  "Invalid credentials.",
             ]
        ];

        return response()->json($data, 401);
    }
}
