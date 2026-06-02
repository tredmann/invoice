<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'is_admin' => true,
        ]);
        $this->be($this->user);
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->tenant->users()->attach($this->user);
        $this->customer = Customer::factory(['tenant_id' => $this->tenant->id])->create();
        $this->invoice = Invoice::factory()
            ->for($this->customer)
            ->create();
    }

    public function testShowGetRoute()
    {
        $this->get($this->tenant->route('invoices.show', ['invoice' => $this->invoice]))->assertStatus(200);
    }

    public function testDestroyDeleteRoute()
    {
        $this->be($this->user); // log in the user again

        $this->delete('http://localhost:8080/'.$this->tenant->id.'/invoices/'.$this->invoice->id)->assertStatus(302);
    }

    public function testGetPdf()
    {
        $legalInfo = LegalInfo::factory()->create();
        $generalInfo = GeneralInfo::factory()->create();

        $this->tenant
            ->currentLegalInfo()
            ->associate($legalInfo)
            ->save();
        $this->tenant
            ->currentGeneralInfo()
            ->associate($generalInfo)
            ->save();

        $this->get(route('invoices.pdf', ['invoice' => $this->invoice, 'tenant' => $this->tenant]))->assertStatus(200);
    }

    public function testNoLineItemOpen()
    {
        $this->actingAs($this->user);

        $request = [
            'days_till_due' => $this->faker->randomElement(Invoice::DAYS_TILL_DUE),
            'performed_when' => $this->faker->word,

            'invoice' => $this->invoice,
        ];

        $this->patch('http://localhost:8080/'.$this->tenant->id.'/invoices/'.$this->invoice->id.'/open', $request)->assertSessionHasErrors(['lineItems' => 'Die Rechnung hat keine Rechnungspositionen!']);
    }

    public function testHasLineItemOpen()
    {
        $legalInfo = LegalInfo::factory()->create();
        $generalInfo = GeneralInfo::factory()->create();

        $this->tenant
            ->currentLegalInfo()
            ->associate($legalInfo)
            ->save();
        $this->tenant
            ->currentGeneralInfo()
            ->associate($generalInfo)
            ->save();

        LineItem::factory()
            ->for($this->invoice)
            ->create();

        $request = [
            'days_till_due' => $this->faker->randomElement(Invoice::DAYS_TILL_DUE),
            'performed_when' => $this->faker->word,

            'invoice' => $this->invoice,
        ];

        $this->patch(route('invoices.open', ['tenant' => $this->tenant, 'invoice' => $this->invoice]), $request)
            ->assertStatus(302)
            ->assertSessionHasAll(['success']);
    }

    public function testPaid()
    {
        $body = [
            'paid_at' => today()->toDateString(),
        ];

        $this->patch($this->tenant->route('invoices.paid', ['invoice' => $this->invoice]), $body)
            ->assertStatus(302)
            ->assertSessionHasAll(['success']);
    }

    public function testOpenInvoiceHasNoLegalInfo()
    {
        $invoice = Invoice::factory(['customer_id' => $this->customer->id])
            ->hasLineItems(5)
            ->create();

        $generalInfo = GeneralInfo::factory()->create();

        $this->tenant
            ->currentGeneralInfo()
            ->associate($generalInfo)
            ->save();

        $request = [
            'days_till_due' => $this->faker->randomElement(Invoice::DAYS_TILL_DUE),
            'performed_when' => $this->faker->word,

            'invoice' => $invoice,
        ];

        $this->patch(route('invoices.open', ['tenant' => $this->tenant, 'invoice' => $invoice]), $request)
            ->assertStatus(302)
            ->assertSessionHasErrors('legalInfo');
    }

    public function testOpenInvoiceHasNoGeneralInfo()
    {
        $invoice = Invoice::factory(['customer_id' => $this->customer->id])
            ->hasLineItems(5)
            ->create();

        $legalInfo = LegalInfo::factory()->create();

        $this->tenant
            ->currentLegalInfo()
            ->associate($legalInfo)
            ->save();

        $request = [
            'days_till_due' => $this->faker->randomElement(Invoice::DAYS_TILL_DUE),
            'performed_when' => $this->faker->word,

            'invoice' => $invoice,
        ];

        $response = $this->patch(route('invoices.open', ['tenant' => $this->tenant, 'invoice' => $invoice]), $request);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('generalInfo');
    }

    public function testSendMailHasNoInvoiceDocument()
    {
        $invoice = Invoice::factory()
            ->for($this->customer)
            ->hasLineItems(5)
            ->create();

        $this->post($this->tenant->route('invoices.sendmail', ['invoice' => $invoice]))
            ->assertStatus(302)
            ->assertSessionHasErrors('invoiceDocument');
    }

    public function testSendMailTenantHasNoMailReceiver()
    {
        $invoice = Invoice::factory()
            ->for($this->customer)
            ->open()
            ->hasLineItems(5)
            ->create();

        $this->post($this->tenant->route('invoices.sendmail', ['invoice' => $invoice]))
            ->assertStatus(302)
            ->assertSessionHasErrors('mailReceiver');
    }

    public function testSendMailTenantHasNoGeneralInfoThenAddOne()
    {
        $invoice = Invoice::factory(['customer_id' => $this->customer->id])
            ->open()
            ->hasLineItems(5)
            ->create();

        $legalInfo = LegalInfo::factory()->create();
        $this->tenant
            ->currentLegalInfo()
            ->associate($legalInfo)
            ->save();

        $this->post(route('invoices.sendmail', ['invoice' => $invoice, 'tenant' => $this->tenant]))
            ->assertStatus(302)
            ->assertSessionHasErrors('generalInfo');

        $generalInfo = GeneralInfo::factory()->create();
        $this->tenant
            ->currentGeneralInfo()
            ->associate($generalInfo)
            ->save();

        $this->post(route('invoices.sendmail', ['invoice' => $invoice, 'tenant' => $this->tenant]))
            ->assertStatus(302)
            ->assertSessionDoesntHaveErrors('generalInfo');
    }
}
