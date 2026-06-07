<?php

namespace App\Http\Resources\V1;

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
            'invoiceId' => $this->invoice_id,
            'quantity' => $this->quantity,
            'priceEach' => $this->price_each,
            'withoutTax' => $this->without_tax,
            'taxRate' => $this->tax_rate,
            'withTax' => $this->with_tax,
            'unit' => $this->unit->value,
            'detail' => $this->detail,
            'detailPlus' => $this->detail_plus,
        ];
    }
}
