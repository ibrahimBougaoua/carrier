<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon;

class PostResource extends JsonResource
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
            'id'        => $this->post_id ? $this->post_id : $this->id,
            'body'      => $this->body,
            'city'      => $this->city->name,
            'city_id'      => $this->city->id,
            'phone'      => $this->user->phone_number,
            'author'       => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'role' => $this->user->role
            ],
            'created_at' => Carbon\Carbon::parse($this->created_at)->format('Y/m/d'),
            'updated_at' => Carbon\Carbon::parse($this->updated_at)->format('Y/m/d'),
        ];
    }
}
