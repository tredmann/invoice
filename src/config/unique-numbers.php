<?php

use App\Models\Invoice;

return [
    Invoice::class => [
        'format' => env('INVOICE_UNIQUE_NUMBERS_FORMAT', 2),
        'length' => env('INVOICE_UNIQUE_NUMBERS_LENGTH', 5),
        'value' => env('INVOICE_UNIQUE_NUMBERS_VALUE', 0)
    ]
];
