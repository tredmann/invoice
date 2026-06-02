<?php

namespace App\Services\LineItems;

use App\Models\Invoice;
use App\Models\LineItem;
use App\Services\Shared\LineItemProcessorService;

class LineItemService
{
    public function store(array $attributes, Invoice $invoice)
    {
        $processedAttributes = LineItemProcessorService::processLineItemData($attributes);

        return $invoice->lineItems()->create($processedAttributes);
    }

    public function update(array $attributes, LineItem $lineItem): LineItem
    {
        $processedAttributes = LineItemProcessorService::processLineItemData($attributes);

        $lineItem->update($processedAttributes);

        return $lineItem;
    }
}
