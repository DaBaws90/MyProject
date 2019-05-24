<?php

namespace App\Http\Controllers\API; 

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; 
use Validator;
use DB;
use Illuminate\Support\Facades\Validator as IlluminateValidator;

class RegisterController extends Controller
{

    public function register(Request $request){

        $validator = Validator::make($request->all(),[
            'name'     => 'required|string',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        if($validator->fails()) { 
            return response()->json(['error' => $validator->errors()], 422); 
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->save();

        $success['token'] = $user->createToken("Personal Access Token")->accessToken;
        $success['user'] = $user;
        return response()->json(['success' => $success], 201);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email'       => 'required|string|email',
            'password'    => 'required|string',
            'remember_me' => 'boolean',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Wrong credentials or unauthorized access'], 401);
        }
        
        $user = $request->user();

        DB::table('oauth_access_tokens')->where('user_id',$user->id)
            ->update(['revoked' => true]);

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        else {
            $token->expires_at = Carbon::now()->addHours(1);
        }
        
        // $token->expires_at = Carbon::now()->addMinutes(1);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse($tokenResult->token->expires_at)
                                                ->toDateTimeString(),
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request){

        $request->user()->token()->revoke();

        return response()->json(['success' => 'Successfully logged out'], 200);
    }


    public function user(Request $request){
        return response()->json(['user' => $request->user()], 200);
    }

    public function editUser(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if ($request->user()->update($request->all())) {
            return response()->json([
                'success' => 'Your profile has been successfully updated',
                'user' => $request->user(),
            ], 200);
        }

        return response()->json(['message' => 'An error occurred while updating the information. Please, try again later.'], 422);
    }
}