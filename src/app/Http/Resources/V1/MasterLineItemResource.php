<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class MasterLineItemResource extends JsonResource
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
            'master_invoice_id' => $this->master_invoice_id,
            'quantity' => $this->quantity,
            'price_each' => $this->price_each,
            'tax_rate' => $this->tax_rate,
            'unit' => $this->unit,
            'detail' => $this->detail,
            'detail_plus' => $this->detail_plus,
        ];
    }
}
