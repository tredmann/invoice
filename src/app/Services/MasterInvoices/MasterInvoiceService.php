<?php

namespace App\Services\MasterInvoices;

use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\MasterInvoice;
use App\Models\MasterLineItem;
use App\Services\Shared\LineItemProcessorService;

class MasterInvoiceService
{
    public function store(array $attributes)
    {
        return MasterInvoice::create($attributes);
    }

    public function active(array $attributes, MasterInvoice $masterInvoice): void
    {
        $attributes['status'] = MasterInvoice::STATUS_ACTIVE;
        $masterInvoice->update($attributes);
    }

    public function pause(MasterInvoice $masterInvoice): void
    {
        $masterInvoice->update(['status' => MasterInvoice::STATUS_PAUSED]);
    }

    public function addMasterLineItem(array $attributes, MasterInvoice $masterInvoice): MasterLineItem
    {
        $attributes = LineItemProcessorService::processLineItemData($attributes);

        return MasterLineItem::create($attributes);
    }

    public static function totalsUpdate(MasterInvoice $masterInvoice): void
    {
        $masterInvoice->unsetRelation('masterLineItems');

        $masterInvoice->update([
            'total_without_tax' => $masterInvoice->masterLineItems->sum('without_tax'),
            'total_with_tax' => $masterInvoice->masterLineItems->sum('with_tax'),
        ]);
    }

    public static function setCurrencyIfNull(MasterInvoice $masterInvoice, MasterLineItem $masterLineItem): void
    {
        $masterInvoice->currency ? null : $masterInvoice->update(['currency' => $masterLineItem->currency]);
    }

    public function getOverdues()
    {
        return MasterInvoice::where('status', '=', MasterInvoice::STATUS_ACTIVE)
            ->whereDate('next_print', '<=', today())
            ->get();
    }

    public function getNextPrint(MasterInvoice $masterInvoice)
    {
        return today()->modify($masterInvoice->billing_frequency);
    }

    public function setNextPrint(MasterInvoice $masterInvoice): void
    {
        $masterInvoice->next_print = $this->getNextPrint($masterInvoice);
        $masterInvoice->update();
    }

    public function convertToInvoice(MasterInvoice $masterInvoice)
    {
        $invoice = Invoice::create([
            'user_id' => $masterInvoice->user_id,
            'customer_id' => $masterInvoice->customer_id,
            'currency' => $masterInvoice->currency,
            'days_till_due' => $masterInvoice->days_till_due,
            'total_with_tax' => $masterInvoice->total_with_tax,
            'total_without_tax' => $masterInvoice->total_without_tax,
        ]);

        foreach ($masterInvoice->masterLineItems as $masterLineItem) {
            LineItem::create([
                'invoice_id' => $invoice->id,
                'user_id' => $masterLineItem->user_id,
                'quantity' => $masterLineItem->quantity,
                'price_each' => $masterLineItem->price_each,
                'currency' => $masterLineItem->currency,
                'without_tax' => $masterLineItem->without_tax,
                'tax_rate' => $masterLineItem->tax_rate,
                'with_tax' => $masterLineItem->with_tax,
                'unit' => $masterLineItem->unit,
                'detail' => $masterLineItem->detail,
                'detail_plus' => $masterLineItem->detail_plus,
            ]);
        }

        return $invoice;
    }
}
