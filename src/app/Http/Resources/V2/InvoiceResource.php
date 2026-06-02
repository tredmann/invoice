<?php

namespace App\Http\Resources\V2;

use App\Models\Invoice;
use App\Services\Invoices\InvoiceService;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'invoice_no' => $this->invoice_no,
            'user_id' => $this->user->id,
            'customer_id' => $this->customer_id,
            'currency' => $this->currency,
            'status' => $this->status,
            $this->mergeWhen($this->status === Invoice::STATUS_OPEN, [
                'performed_when' => $this->performed_when,
                'open_at' => $this->open_at,
                'days_till_due' => $this->days_till_due,
                'date_due' => $this->date_due,
            ]),
            'total_with_tax' => $this->total_with_tax,
            'total_without_tax' => $this->total_without_tax,
            'taxes' => [
                'total' => $this->total_with_tax - $this->total_without_tax,
                'values' => InvoiceService::totalPerTax($this->lineItems),
            ],
            'lines' => LineItemResource::collection($this->lineItems),
        ];
    }
}
