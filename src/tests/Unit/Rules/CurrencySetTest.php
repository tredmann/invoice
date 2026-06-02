<?php

namespace Tests\Unit\Rules;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Rules\CurrencySet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CurrencySetTest extends TestCase
{
    use DatabaseTransactions;

    public function testPassesWhenInvoiceCurrencyIsNull(): void
    {
        $invoice = $this->makeInvoice(['currency' => null]);
        $rule = new CurrencySet($invoice, 'EUR');

        self::assertTrue($rule->passes('currency', 'EUR'));
    }

    public function testPassesWhenCurrencyMatches(): void
    {
        $invoice = $this->makeInvoice(['currency' => 'EUR']);
        $rule = new CurrencySet($invoice, 'EUR');

        self::assertTrue($rule->passes('currency', 'EUR'));
    }

    public function testFailsWhenCurrencyDiffers(): void
    {
        $invoice = $this->makeInvoice(['currency' => 'EUR']);
        $rule = new CurrencySet($invoice, 'USD');

        self::assertFalse($rule->passes('currency', 'USD'));
    }

    private function makeInvoice(array $overrides): Invoice
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Tenant $tenant */
        $tenant = Tenant::factory(['owner_id' => $user->id])->create();
        /** @var Customer $customer */
        $customer = Customer::factory(['tenant_id' => $tenant->id])->create();

        /** @var Invoice $invoice */
        $invoice = Invoice::factory()->for($customer)->create($overrides);

        return $invoice;
    }
}
