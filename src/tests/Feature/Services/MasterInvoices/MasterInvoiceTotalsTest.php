<?php

namespace Tests\Feature\Services\MasterInvoices;

use App\Enums\UnitCode;
use App\Models\Customer;
use App\Models\MasterInvoice;
use App\Models\MasterLineItem;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\AssertsMoney;
use Tests\TestCase;

class MasterInvoiceTotalsTest extends TestCase
{
    use DatabaseTransactions;
    use AssertsMoney;

    private function makeMasterInvoice(): MasterInvoice
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Tenant $tenant */
        $tenant = Tenant::factory(['owner_id' => $user->id])->create();
        /** @var Customer $customer */
        $customer = Customer::factory(['tenant_id' => $tenant->id])->create();

        /** @var MasterInvoice $master */
        $master = MasterInvoice::factory()->for($customer)->create();
        return $master;
    }

    private function addLineItem(MasterInvoice $master, float $quantity, int $priceCents, float $taxRate): MasterLineItem
    {
        return MasterLineItem::create([
            'master_invoice_id' => $master->id,
            'user_id' => $master->user_id,
            'quantity' => $quantity,
            'price_each' => $priceCents,
            'currency' => 'EUR',
            'without_tax' => (int) round($priceCents * $quantity),
            'tax_rate' => $taxRate,
            'with_tax' => (int) round(($priceCents * $quantity) * (1 + $taxRate)),
            'unit' => UnitCode::Hour->value,
            'detail' => 'work',
        ]);
    }

    public function testCreatingMasterLineItemUpdatesTotals(): void
    {
        $master = $this->makeMasterInvoice();
        $this->addLineItem($master, 1.0, 1000, 0.19);

        $this->assertCentsEqual(1190, $master->fresh()->total_with_tax);
        $this->assertCentsEqual(1000, $master->fresh()->total_without_tax);
    }

    public function testUpdatingMasterLineItemUpdatesTotals(): void
    {
        $master = $this->makeMasterInvoice();
        $item = $this->addLineItem($master, 1.0, 1000, 0.19);

        $item->update(['quantity' => 2.0, 'without_tax' => 2000, 'with_tax' => 2380]);

        $this->assertCentsEqual(2380, $master->fresh()->total_with_tax);
    }

    public function testDeletingMasterLineItemUpdatesTotals(): void
    {
        $master = $this->makeMasterInvoice();
        $a = $this->addLineItem($master, 1.0, 1000, 0.19);
        $this->addLineItem($master, 1.0, 500, 0.19);

        $a->delete();

        $this->assertCentsEqual(595, $master->fresh()->total_with_tax);
    }
}
