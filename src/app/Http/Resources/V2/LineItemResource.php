<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class LineItemResource extends JsonResource
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
            'invoice_id' => $this->invoice_id,
            'quantity' => $this->quantity,
            'price_each' => $this->price_each,
            'without_tax' => $this->without_tax,
            'tax_rate' => $this->tax_rate,
            'with_tax' => $this->with_tax,
            'unit' => $this->unit,
            'detail' => $this->detail,
            'detail_plus' => $this->detail_plus,
        ];
    }
}
