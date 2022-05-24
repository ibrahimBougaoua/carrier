<?php

namespace App\Models;

use App\Traits\Multitenancy;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,Multitenancy,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
        'dateofbirth',
        'about_me',
        'email',
        'role',
        'city_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function scopeLatest()
    {
        return $this->orderBy('created_at', 'desc');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id')->latest();
    }

    public function allPosts()
    {
        return $this->hasMany(Post::class, 'user_id')->latest();
    }

    public function allPostsWithGlobalScope()
    {
        return $this->hasMany(Post::class, 'user_id')->withoutGlobalScope('user_id');
    }

    public function following() {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }

    public function nbrOfFollowing() {
        return $this->following()->count();
    }

    public function followers() {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id');
    }

    public function nbrOfFollowers() {
        return $this->followers()->count();
    }

    public function isFollowing(User $user)
    {
        return !! $this->following()->where('following_id', $user->id)->count();
    }

    public function isFollowedBy(User $user)
    {
        return !! $this->followers()->where('user_id', $user->id)->count();
    }
}
