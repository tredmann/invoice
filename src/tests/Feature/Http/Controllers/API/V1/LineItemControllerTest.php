<?php

namespace Tests\Feature\Http\Controllers\API\V1;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LineItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->tenant->users()->attach($this->user);
        $this->customer = Customer::factory()->create();
        $this->invoice = Invoice::factory()
            ->for($this->customer)
            ->create();
        $this->lineItem = LineItem::factory()
            ->for($this->invoice)
            ->create();
    }

    public function testLineItemStore1()
    {
        $request = [
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => 'Website',
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
            'currency' => $this->invoice->currency,
        ];

        $request['invoice'] = $this->invoice;

        $this->postJson($this->tenant->route('api.v1.lineItems.store', $request))->assertCreated();
    }

    public function testLineItemUpdate()
    {
        $request = [
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => 'Website',
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
            'currency' => $this->invoice->currency,
        ];

        $request['lineItem'] = $this->lineItem;

        $this->patchJson($this->tenant->route('api.v1.lineItems.update', $request))->assertOk();
    }

    public function testLineItemDestroy()
    {
        $this->deleteJson(
            $this->tenant->route('api.v1.lineItems.destroy', ['lineItem' => $this->lineItem]),
        )->assertNoContent();
    }
}
