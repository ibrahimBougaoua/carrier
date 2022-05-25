<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/user/profile/update', [AuthController::class, 'update'])->name('user.update');

    Route::get('/user/profile/{id}', [AuthController::class, 'profile'])->name('show.user');
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/user/has/follow/{id}', [UserController::class, 'hasFollow'])->name('has.follow.user');
    Route::post('/user/follow/{user_id}', [UserController::class, 'follow'])->name('follow.user');
    Route::post('/user/unfollow/{user_id}', [UserController::class, 'unFollow'])->name('unFollow.user');
    Route::get('/user/followers', [UserController::class, 'followers'])->name('followers.user');
    Route::get('/user/following', [UserController::class, 'following'])->name('following.user');
    Route::get('/dashboard', [PostController::class, 'dashboard'])->name('admin.dashboard');
    Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');

    Route::get('/search/posts/{value}', [PostController::class, 'search'])->name('search.posts');
    Route::get('/post/show/{id}', [PostController::class, 'show'])->name('post.show');
    Route::get('/my/posts', [PostController::class, 'posts'])->name('my.posts');
    Route::post('/post/new', [PostController::class, 'store'])->name('post.new');
    Route::get('/posts', [PostController::class, 'index'])->name('posts');
    Route::get('/related/posts/by/city/{id}', [PostController::class, 'relatedPostsByCity'])->name('relatedPostsByCity');
    Route::get('/abonnements/posts', [PostController::class, 'abonnementsPosts'])->name('abonnements.posts');
    Route::get('/recommended/posts', [PostController::class, 'recommended'])->name('recommended.posts');
    Route::put('/post/edit/{id}', [PostController::class, 'update'])->name('post.edit');
    Route::delete('/post/delete/{id}', [PostController::class, 'destroy'])->name('post.delete');
});

Route::get('/is/admin', [AuthController::class, 'isAdmin'])->name('is.admin');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
