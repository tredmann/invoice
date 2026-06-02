<?php

namespace App\Services\API\V2;

use App\Http\Requests\API\V2\InvoiceRequest;
use App\Jobs\GeneratePDF;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\UniqueNumber;
use App\Services\Shared\LineItemProcessorService;
use Illuminate\Support\Carbon;

class InvoiceService
{
    public static function store(InvoiceRequest $request): Invoice
    {
        $input = $request->validated();

        $invoice = Invoice::create($input);

        foreach ($input['lines'] as $line) {
            self::addLineItem($invoice, $line);
        }

        if ($invoice->status === Invoice::STATUS_OPEN) {
            self::open($invoice, $input);
        }

        return $invoice->refresh();
    }

    public static function addLineItem(Invoice $invoice, array $attributes): LineItem
    {
        $attributes += [
            'invoice_id' => $invoice->id,
            'currency' => $invoice->currency,
        ];

        $attributes = LineItemProcessorService::processLineItemData($attributes);

        return LineItem::create($attributes);
    }

    public static function open(Invoice $invoice, array $attributes): void
    {
        $attributes += [
            'date_due' => Carbon::parse($attributes['open_at'])->addDays((int) $attributes['days_till_due']),
            'invoice_no' => UniqueNumber::generateNumber(
                model:$invoice,
                format: config('unique-numbers.'.Invoice::class.'.format', 2)
            ),
        ];

        $invoice->update($attributes);

        GeneratePDF::dispatch($invoice);
    }
}
