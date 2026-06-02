<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class MasterInvoiceResource extends JsonResource
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
            'customerId' => $this->customer_id,
            'billingFrequency' => $this->billing_frequency,
            'daysTillDue' => $this->days_till_due,
            'nextPrint' => $this->next_print,
            'status' => $this->status,
            'masterLineItems' => LineItemResource::collection($this->masterLineItems),
        ];
    }
}
