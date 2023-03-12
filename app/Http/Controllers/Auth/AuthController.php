<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
        info('request',[$request]);
        $credentials = request(['email', 'password']);
        info('credentials',[$credentials]);
        // print_r($credentials);die;
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ],401);
        $user = $request->user();
        info('user',[$user]);
        $tokenResult = $user->createToken('Personal Access Token',[]);
        info('tokenResult',[$tokenResult]);
        $token = $tokenResult->token;
        info('token',[$token]);
        info('remember me',[$request->remember_me]);
         if ($request->remember_me)
             $token->expires_at = Carbon::now()->addWeeks(1);
        info('token expires',[$token->expires_at]);
         $token->save();
        info('response',['access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)
                ->toDateTimeString()]);
         return response()->json([
             'access_token' => $tokenResult->accessToken,
             'token_type' => 'Bearer',
             'expires_at' => Carbon::parse(
                 $tokenResult->token->expires_at
             )->toDateTimeString()
         ]);
   }
    public function register(Request $request)
    {
        $request->validate([
            'fName' => 'required|string',
            'lName' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
          ]);
          $user = new User;
          $user->first_name = $request->fName;
          $user->last_name = $request->lName;
          $user->email = $request->email;
          $user->password = bcrypt($request->password);
          $user->save();
          return response()->json([
              'message' => 'Successfully created user!'
          ], 201);
   }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out' ],200);
}
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
