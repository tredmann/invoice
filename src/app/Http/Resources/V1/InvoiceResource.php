<?php

namespace App\Http\Resources\V1;

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
            'invoiceNo' => $this->invoice_no,
            'customerId' => $this->customer_id,
            'performedWhen' => $this->performed_when,
            'daysTillDue' => $this->days_till_due,
            'dateDue' => $this->date_due,
            'status' => $this->status,
            'mailStatus' => $this->mail_status,
            'totalWithTax' => $this->total_with_tax,
            'totalWithoutTax' => $this->total_without_tax,
            'taxes' => [
                'total' => $this->total_with_tax - $this->total_without_tax,
                'values' => InvoiceService::totalPerTax($this->lineItems),
            ],
            'lineItems' => LineItemResource::collection($this->lineItems),
        ];
    }
}
