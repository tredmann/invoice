<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $this->resource;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'company' => $user->company,
            'street' => $user->street,
            'postal' => $user->postal,
            'city' => $user->city,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
        ];
    }
}
