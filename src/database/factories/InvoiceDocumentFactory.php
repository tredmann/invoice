<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InvoiceDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'invoice_id' => fn () => Invoice::factory()->create()->id,
            'user_id' => fn (array $attributes) => Invoice::find($attributes['invoice_id'])?->user_id
                ?? User::factory()->create()->id,
            'path' => 'invoices/'.$this->faker->uuid().'.pdf',
            'storage' => 'local',
            'mime_type' => 'application/pdf',
            'type' => InvoiceDocument::TYPE_INVOICE_DOCUMENT,
        ];
    }

    public function attachment(): self
    {
        return $this->state(fn () => ['type' => InvoiceDocument::TYPE_ATTACHMENT]);
    }

    public function invoiceDocument(): self
    {
        return $this->state(fn () => ['type' => InvoiceDocument::TYPE_INVOICE_DOCUMENT]);
    }
}
