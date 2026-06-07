<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\UnitCode;
use App\Models\Customer;
use App\Models\MasterInvoice;
use App\Models\MasterLineItem;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterLineItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->tenant->users()->attach($this->user);
        $this->customer = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->masterInvoice = MasterInvoice::factory()
            ->for($this->customer)
            ->create();
        $this->masterLineItem = MasterLineItem::factory()
            ->for($this->masterInvoice)
            ->create();
    }

    public function testMasterLineItemCreate()
    {
        $response = $this->get(
            route('masterLineItems.create', ['masterInvoice' => $this->masterInvoice, 'tenant' => $this->tenant]),
        );

        $response->assertSuccessful();
    }

    public function testMasterLineItemStore1()
    {
        $request = [
            'master_invoice_id' => $this->masterInvoice->id,
            'user_id' => $this->user->id,
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
            'currency' => $this->masterInvoice->currency,
        ];

        $request['masterInvoice'] = $this->masterInvoice;
        $request['submit'] = 'done';

        $response = $this->post(route('masterLineItems.store', ['tenant' => $this->tenant]), $request);
        $response->assertRedirect();
    }

    public function testMasterLineItemUpdate()
    {
        $request = [
            'master_invoice_id' => $this->masterInvoice->id,
            'user_id' => $this->user->id,
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
            'currency' => $this->masterInvoice->currency,
        ];

        $request['masterLineItem'] = $this->masterLineItem;

        $this->patch($this->tenant->route('masterLineItems.update', $request))->assertRedirect(
            $this->tenant->route('masterInvoices.masterLineItems', ['masterInvoice' => $this->masterInvoice]),
        );
    }

    public function testMasterLineItemDestroy()
    {
        $this->delete(
            route('masterLineItems.destroy', ['masterLineItem' => $this->masterLineItem, 'tenant' => $this->tenant]),
        )->assertRedirect(
            route('masterInvoices.masterLineItems', ['masterInvoice' => $this->masterInvoice, 'tenant' => $this->tenant]),
        );
    }

    public function testMasterLineItemEdit()
    {
        $this->get(
            route('masterLineItems.edit', ['masterLineItem' => $this->masterLineItem, 'tenant' => $this->tenant]),
        )->assertOk();
    }
}
