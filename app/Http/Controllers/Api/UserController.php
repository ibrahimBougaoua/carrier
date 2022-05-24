<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $users = User::where('id','!=',Auth::user()->id)->latest()->get();

            if( ! $users )
                return $this->failure('Users not found.');

            return $this->success(
                'Disaply users successfully.',
                [
                    'users' => UserResource::collection($users),
                    'count' => $users->count(),
                ]
            );

        } catch(Exception $e){
            return $this->failure('This Post not exists.');
        }
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

    public function hasFollow(Request $request,$id)
    {
        try {
            $checkUser = User::find($id);
            if ($request->user()->isFollowing($checkUser) == 1) {
                return $this->success(
                    'User Follow successfully.',
                );
            } else {
                return $this->failure('User not exists.');
            }

        } catch(Exception $e){
            return $this->failure('This User not exists.');
        }
    }

    public function follow(Request $request)
    {
        try {

            $user = User::find($request->user_id);

            if( ! $user )
                return $this->failure('User not exists.');

            $follow = Auth::user()->following()->attach($user);

            return $this->success(
                'User Follow successfully.',
            );

        } catch(Exception $e){
            return $this->failure('This User not exists.');
        }
    }

    public function unFollow(Request $request)
    {
        try {

            $user = User::find($request->user_id);

            if( ! $user )
                return $this->failure('User not exists.');

            $follow = Auth::user()->following()->detach($user);

            return $this->success(
                'User unFollow successfully.',
            );

        } catch(Exception $e){
            return $this->failure('This User not exists.');
        }
    }

    public function followers()
    {
        try {
            $followers = Auth::user()->followers()->get();

            return $this->success(
                'User followers successfully.',
                [
                    'followers' => $followers
                ]
            );

        } catch(Exception $e){
            return $this->failure('This User not exists.');
        }
    }

    public function following()
    {
        try {
            $following = Auth::user()->following()->get();

            return $this->success(
                'User following successfully.',
                [
                    'following' => $following
                ]
            );

        } catch(Exception $e){
            return $this->failure('This User not exists.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if(Auth::user()->role !== 'admin')
                return $this->failure('This User not exists.');

            $user = User::where('id','=',$id);

            if( ! $user )
                return $this->failure('User not exists.');

            $user->delete();

            $post = Post::withoutGlobalScope('user_id')->where('user_id','=',$id);
            $post->delete();

            return $this->success(
                'User successfully deleted.',
            );

        } catch(Exception $e){
            return $this->failure('This User not exists.');
        }
    }

}
