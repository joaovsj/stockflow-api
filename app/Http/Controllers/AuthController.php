<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use \App\Components\AuthComponent; 
use \App\Http\Requests\StoreUserRequest;
use \App\Http\Requests\LoginRequest;
use \App\Models\User;
use \App\Models\UserImage;

class AuthController extends Controller
{   
    public function register(StoreUserRequest $request){
    
        $fields = $request->validated();

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => $fields['password'] 
        ]);

        return response()->json([
            'status' => true,
            'name'   => $user->name,
            'email'  => $user->email,
            'user_id'   => base64_encode($user->id),
            'token' => $user->createToken('userLogged')->plainTextToken
        ], 201);

    }

    public function login(LoginRequest $request){

        $fields = $request->validated();
        $user = User::where('email', $fields['email'])->first();


        if($user){
            if(Hash::check($fields['password'], $user->password)) {

                $image = UserImage::where('user_id', $user->id)->first();

                return response()->json([
                    'status' => true,
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'role'   => $user->role,
                    'img' => $image->name ?? '',
                    'user_id'   => base64_encode($user->id),
                    'token'  => $user->createToken('userLogged')->plainTextToken
                ], 200);
            }
        }

        return response()->json([
            'status' => false,
            'message'  => "Usuário ou senha inválidos!"
        ], 401);

    }

    public function logout(){
        
    }
}
