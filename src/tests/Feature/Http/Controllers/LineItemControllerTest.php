<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\UnitCode;
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
        $this->customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->invoice = Invoice::factory()
            ->for($this->customer)
            ->create();
        $this->lineItem = LineItem::factory()
            ->for($this->invoice)
            ->create();
    }

    public function testLineItemCreate()
    {
        $this->get($this->tenant->route('lineItems.create', ['invoice' => $this->invoice]))->assertSuccessful();
    }

    public function testLineItemStore1()
    {
        $request = [
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
            'currency' => $this->invoice->currency,
        ];

        $request['submit'] = 'done';

        $request['invoice'] = $this->invoice;

        $this->post($this->tenant->route('lineItems.store', $request))->assertRedirect(
            $this->tenant->route('customers.invoices', ['customer' => $this->customer]),
        );
    }

    public function testLineItemUpdate()
    {
        $request = [
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
            'currency' => $this->invoice->currency,
        ];

        $request['lineItem'] = $this->lineItem;

        $this->patch(route('lineItems.update', ['tenant' => $this->tenant, 'lineItem' => $this->lineItem]), $request)->assertRedirect(
            route('invoices.show', ['invoice' => $this->invoice, 'tenant' => $this->tenant]),
        );
    }

    public function testLineItemDestroy()
    {
        $this->delete($this->tenant->route('lineItems.destroy', ['lineItem' => $this->lineItem]))->assertRedirect(
            $this->tenant->route('invoices.show', ['invoice' => $this->invoice]),
        );
    }

    public function testLineItemEdit()
    {
        $this->get(route('lineItems.edit', ['lineItem' => $this->lineItem, 'tenant' => $this->tenant]))->assertOk();
    }

    public function testLineItemStoreFailsWithInvalidUnit()
    {
        $request = [
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => 'invalid_unit_string',
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
            'currency' => $this->invoice->currency,
            'submit' => 'done',
            'invoice' => $this->invoice,
        ];

        $this->post($this->tenant->route('lineItems.store', $request))->assertSessionHasErrors(['unit']);
    }
}
