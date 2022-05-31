<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use Twilio\Rest\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ResponseTrait;

    public function register(Request $request)
    {
        //return $request->all();
        /*$validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string',
            'dateofbirth' => 'required',
            'about_me' => 'required',
            'role' => 'required',
            'city_id' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            return $this->failure(
                'user registerd error.',

                [
                    'errors' => $validator,
                ]

                );
        }*/

        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'dateofbirth' => $request->dateofbirth,
            'about_me' => $request->about_me,
            'role' => $request->role,
            'city_id' => $request->city_id,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(
            'user registerd successfully.',
            [
                'user' => new UserResource($user),
                'token' => $token,
            ]
        );
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return $this->success(
                    'Hi '.$user->name.', welcome to home',
                    [
                        'user' => new UserResource($user),
                        'token' => $token,
                    ]
                );
            } else {
                return $this->failure('Unauthorized.');
            }
        } else {
            return $this->failure('Unauthorized.');
        }
    }

    public function me(Request $request)
    {
        return $this->success(
            'Hi '.$request->user()->name.', welcome to home',
            [
                'user' => new UserResource($request->user()),
            ]
        );
    }

    public function isAdmin()
    {
            return $this->success(
                'Hi welcome to home',
                [
                    'isAdmin' => Auth::user()->role == 'admin' ? true : false,
                ]
            );
    }

    public function profile(Request $request)
    {
        $user = User::find($request->id);
        return $this->success(
            'Hi '.$user->name.', welcome to home',
            [
                'user' => new UserResource($user),
            ]
        );
    }

    public function update(Request $request)
    {
        try {
            $user = User::find($request->id);

            if( ! $user )
                return $this->failure('User not found.');

            if( ! empty($request->email) )
                $user->email = $request->email;

            if( ! empty($request->name) )
                $user->name = $request->name;

            if( ! empty($request->phone_number) )
                $user->phone_number = $request->phone_number;

            if( ! empty($request->about_me) )
                $user->about_me = $request->about_me;

            if( ! empty($request->password) )
                $user->password = Hash::make($request->password);

            if( ! empty($request->city_id) )
                $user->city_id = $request->city_id;

            $user->save();

            return $this->success(
                'User updated successfully.',
            );

        } catch(Exception $e){
            return $this->failure('This user not exists.');
        }
    }

    // method for user logout and delete token
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }

    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255|unique:users,email,'.$request->user()->id,
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $user = $request->user();
        $user->email = $request->email;
        $user->save();

        return [
            'message' => 'You\'r email successfully Updated'
        ];
    }

    public function updatePhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:255|unique:users,phone_number,'.$request->user()->id,
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $user = $request->user();
        $user->phone_number  = $request->phone_number ;
        $user->save();

        return [
            'message' => 'You\'r phone number  successfully Updated'
        ];
    }
}
