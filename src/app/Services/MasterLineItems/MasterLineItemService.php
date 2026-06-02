<?php

namespace App\Services\MasterLineItems;

use App\Models\MasterLineItem;
use App\Services\Shared\LineItemProcessorService;

class MasterLineItemService
{
    public function store(array $attributes): MasterLineItem
    {
        $processedAttributes = LineItemProcessorService::processLineItemData($attributes);

        return MasterLineItem::create($processedAttributes);
    }

    public function update(array $attributes, MasterLineItem $masterLineItem): MasterLineItem
    {
        $processedAttributes = LineItemProcessorService::processLineItemData($attributes);

        $masterLineItem->update($processedAttributes);

        return $masterLineItem;
    }
}
