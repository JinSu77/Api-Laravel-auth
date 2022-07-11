<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validate -> fails())
        {
            return response()->json(['status_code'=> 400, 'message'=>'Bad Request']);
        }

        $user= new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response() -> json([
            'status_code'=> 200,
            'message'=> 'Utilisateur créer'
        ]);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if($validate -> fails())
        {
            return response()->json(['status_code'=> 400, 'message'=>'Bad Request']);
        }

        $reference = request(['email','password']);

        if(!Auth::attempt($reference))
        {
            return response()->json([
                'status_code'=> 500,
                'message' => 'l\'email ou le mot de passe est incorrect'
            ]);
        }

        $user = User::where('email',$request->email)->first(); 

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status_code'=> 200,
            'token'=> $tokenResult,
            'message'=> 'Vous êtes connecté'
        ]);
    }
}