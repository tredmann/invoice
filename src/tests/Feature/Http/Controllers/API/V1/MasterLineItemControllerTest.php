<?php

namespace Tests\Feature\Http\Controllers\API\V1;

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
        $this->customer = Customer::factory()->create();
        $this->masterInvoice = MasterInvoice::factory()
            ->for($this->customer)
            ->create();
        $this->masterLineItem = MasterLineItem::factory()
            ->for($this->masterInvoice)
            ->create();
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

        $this->postJson(route('api.v1.masterLineItems.store', ['tenant' => $this->tenant]), $request)->assertCreated();
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

        $this->patchJson($this->tenant->route('api.v1.masterLineItems.update', ['masterLineItem' => $this->masterLineItem]), $request)->assertOk();
    }

    public function testMasterLineItemDestroy()
    {
        $this->deleteJson(
            $this->tenant->route('api.v1.masterLineItems.destroy', ['masterLineItem' => $this->masterLineItem]),
        )->assertNoContent();
    }

    public function testMasterLineItemUnitIsSerializedAsString(): void
    {
        $response = $this->getJson(
            route('api.v1.masterInvoices.masterLineItems', ['tenant' => $this->tenant, 'masterInvoice' => $this->masterInvoice]),
        )->assertOk();

        $unit = $response->json('data.masterLineItems.0.unit');

        $this->assertIsString($unit);
        $this->assertSame($this->masterLineItem->unit->value, $unit);
    }
}
