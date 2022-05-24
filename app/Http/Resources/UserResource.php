<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'about_me' => $this->about_me,
            'email' => $this->email,
            'role' => $this->role,
            'city' => $this->city->name,
            'city_id' => $this->city->id,
            'following' => $this->nbrOfFollowing(),
            'followers' => $this->nbrOfFollowers(),
            'posts' =>  PostResource::collection($this->allPostsWithGlobalScope),
            'created_at' => Carbon\Carbon::parse($this->created_at)->format('Y/m/d'),
            'updated_at' => Carbon\Carbon::parse($this->updated_at)->format('Y/m/d'),
        ];
    }
}
