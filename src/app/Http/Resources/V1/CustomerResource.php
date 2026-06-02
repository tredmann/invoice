<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer_no' => $this->customer_no,
            'company_name' => $this->company_name,
            'contact_person' => $this->contact_person,
            'street' => $this->street,
            'postal' => $this->postal,
            'city' => $this->city,
        ];
    }
}
