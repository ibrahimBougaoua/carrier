<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Resources\PostResource;
use Auth;

class PostController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($value)
    {
        try {

            $posts = Post::search($value)->query(function ($builder) {
                $builder->withoutGlobalScopes();
            })->get();

            return $this->success(
                'Search results successfully.',
                [
                    'posts' => $posts,
                ]
            );

        } catch(Exception $e){
            return $this->failure('No result found.');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        try {

            $posts = Post::latest()->withoutGlobalScope('user_id')->get();
            $postsOnlyTrashed = Post::onlyTrashed()->latest()->withoutGlobalScope('user_id')->get();
            $users = User::latest()->get();
            $usersOnlyTrashed = User::onlyTrashed()->latest()->get();

            if( ! $posts )
                return $this->failure('Posts not found.');

            return $this->success(
                'Dashboard results successfully.',
                [
                    'posts' => $posts->count(),
                    'postsOnlyTrashed' => $postsOnlyTrashed->count(),
                    'users' => $users->count(),
                    'usersOnlyTrashed' => $usersOnlyTrashed->count(),
                ]
            );

        } catch(Exception $e){
            return $this->failure('This Post not exists.');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $posts = Post::latest()->withoutGlobalScope('user_id')->get();

            if( ! $posts )
                return $this->failure('Posts not found.');

            return $this->success(
                'Disaply Posts successfully.',
                [
                    'posts' => PostResource::collection($posts),
                ]
            );

        } catch(Exception $e){
            return $this->failure('This Post not exists.');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function recommended()
    {
        try {

            $posts = Post::latest()->where('city_id','=',Auth::user()->city_id)->withoutGlobalScope('user_id')->get();

            if( ! $posts )
                return $this->failure('Posts not found.');

            return $this->success(
                'Disaply Posts successfully.',
                [
                    'posts' => PostResource::collection($posts),
                    'count' => $posts->count(),
                ]
            );

        } catch(Exception $e){
            return $this->failure('This Post not exists.');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function posts()
    {
        try {

            $posts = Post::latest()->where('user_id','=',Auth::user()->id)->get();

            if( ! $posts )
                return $this->failure('Posts not exists.');

            return $this->success(
                'Disaply Post successfully.',
                [
                    'posts' =>  PostResource::collection($posts),
                    'role' => Auth::user()->role,
                    'count' => $posts->count(),
                ]
            );

        } catch(Exception $e){
            return $this->failure('This Post not exists.');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function relatedPostsByCity($city)
    {
        try {

            $posts = Post::latest()->where('city_id','=',$city)->withoutGlobalScope('user_id')->limit(6)->get();

            if( ! $posts )
                return $this->failure('Posts not exists.');

            return $this->success(
                'Disaply Post successfully.',
                [
                    'posts' =>  PostResource::collection($posts),
                    'role' => Auth::user()->role,
                    'count' => $posts->count(),
                ]
            );

        } catch(Exception $e){
            return $this->failure('This Post not exists.');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function abonnementsPosts()
    {
        try {
            $posts = Post::withoutGlobalScope('user_id')->join('followers', function ($join) {
                    $join->on('posts.user_id', '=', 'followers.following_id')
                         ->where('followers.follower_id','=',Auth::user()->id);
                    })->select('*','posts.id as post_id')->get();

            if( ! $posts )
                return $this->failure('Posts not exists.');

            return $this->success(
                'Disaply Posts successfully.',
                [
                    'posts' =>  PostResource::collection($posts),
                    'countAbonne' => Auth::user()->nbrOfFollowing(),
                    'count' => $posts->count(),
                ]
            );

        } catch(Exception $e){
            return $this->failure('This Post not exists.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $post = Post::create([
                'body' => $request->body,
                'city_id' => Auth::user()->city_id,
            ]);

            return $this->success(
                'You have successfully created Post.',
                [
                    'post' => new PostResource($post),
                ]
            );

        } catch(Exception $e){
            return $this->failure('Error create Post.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            if(Auth::user()->role == 'admin')
                $post = Post::withoutGlobalScope('user_id')->find($id);
            else
                $post = Post::find($id);

            if( ! $post )
                return $this->failure('Post not exists.');

            return $this->success(
                'Disaply Post successfully.',
                [
                    'post' => new PostResource($post),
                    'role' => Auth::user()->role,
                ]
            );

        } catch(Exception $e){
            return $this->failure('This Post not exists.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            if(Auth::user()->role == 'admin')
                $post = Post::withoutGlobalScope('user_id')->find($id);
            else
                $post = Post::find($id);

            if( ! $post )
                return $this->failure('Post not exists.');

            $post->body = $request->body;
            $post->save();

            return $this->success(
                'You have successfully update Post.',
                [
                    'post' => $post,
                ]
            );

        } catch(Exception $e){
            return $this->failure('This Post not exists.');
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
            if(Auth::user()->role == 'admin')
                $post = Post::withoutGlobalScope('user_id')->where('id','=',$id);
            else
                $post = Post::where('id','=',$id);

            if( ! $post )
                return $this->failure('Post not exists.');

            $post->delete();

            return $this->success(
                'Post successfully deleted.',
            );

        } catch(Exception $e){
            return $this->failure('This Post not exists.');
        }
    }


}
