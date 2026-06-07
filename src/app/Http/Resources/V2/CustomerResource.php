<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'customer_no' => $this->customer_no,
            'tenant' => new TenantResource($this->tenant),
            'company' => $this->company,
            'name' => $this->name,
            'street' => $this->street,
            'postal' => $this->postal,
            'city' => $this->city,
            'country' => $this->country,
        ];
    }
}
