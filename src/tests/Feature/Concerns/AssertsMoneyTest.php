<?php

namespace Tests\Feature\Concerns;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\AssertionFailedError;
use Tests\Concerns\AssertsMoney;
use Tests\TestCase;

class AssertsMoneyTest extends TestCase
{
    use RefreshDatabase;
    use AssertsMoney;

    public function testAssertCentsEqualPassesOnInt(): void
    {
        $this->assertCentsEqual(199, 199);
    }

    public function testAssertCentsEqualFailsOnMismatch(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->assertCentsEqual(199, 200);
    }

    public function testAssertInvoiceTotalsMatchLineItems(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Tenant $tenant */
        $tenant = Tenant::factory(['owner_id' => $user->id])->create();
        /** @var Customer $customer */
        $customer = Customer::factory(['tenant_id' => $tenant->id])->create();
        /** @var Invoice $invoice */
        $invoice = Invoice::factory()->for($customer)->create();

        LineItem::create([
            'invoice_id' => $invoice->id,
            'user_id' => $user->id,
            'quantity' => 2.0,
            'price_each' => 1000,
            'currency' => 'EUR',
            'without_tax' => 2000,
            'tax_rate' => 0.19,
            'with_tax' => 2380,
            'unit' => 'h',
            'detail' => 'work',
        ]);

        /** @var Invoice $fresh */
        $fresh = $invoice->fresh();
        $this->assertInvoiceTotalsMatchLineItems($fresh);
    }

    public function testAssertCentsEqualRejectsNull(): void
    {
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->assertCentsEqual(0, null);
    }

    public function testAssertCentsEqualRejectsNonNumericString(): void
    {
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->assertCentsEqual(0, 'abc');
    }

    public function testAssertCentsEqualAcceptsNumericString(): void
    {
        $this->assertCentsEqual(199, '199');
    }

    public function testAssertInvoiceTotalsMatchLineItemsRejectsNullTotals(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Tenant $tenant */
        $tenant = Tenant::factory(['owner_id' => $user->id])->create();
        /** @var Customer $customer */
        $customer = Customer::factory(['tenant_id' => $tenant->id])->create();
        /** @var Invoice $invoice */
        $invoice = Invoice::factory()->for($customer)->create();
        $invoice->update(['total_without_tax' => null, 'total_with_tax' => null]);

        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->assertInvoiceTotalsMatchLineItems($invoice->fresh());
    }
}
