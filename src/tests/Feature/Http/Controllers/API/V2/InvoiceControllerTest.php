<?php

namespace Tests\Feature\Http\Controllers\API\V2;

use App\Enums\UnitCode;
use App\Helpers\CamelConverter;
use App\Http\Resources\V2\InvoiceResource;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Money;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class InvoiceControllerTest extends \Tests\TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);

        $this->tenant = Tenant::factory()->create();
        $this->tenant->users()->attach($this->user);

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

        $this->customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
        ]);

        $invoice = [
            'customer_id' => $this->customer->id,
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
        ];

        $lineItem = [
            'quantity' => 231345,
            'price_each' => 34,
            'tax_rate' => 0.07,
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
        ];

        $body = [
            'lines' => [
                $lineItem,
            ],
        ];

        $this->body = array_merge($invoice, $body);
    }

    public function testGetAllInvoicesCurrentTenant()
    {
        $this->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $this->body);
        $response = $this->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $this->body);
        $response->assertStatus(200);

        $response = $this->get('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices');
        $response->assertStatus(200);

        $invoices = $this->tenant->invoices;

        $arr = [];
        foreach ($invoices as $invoice) {
            $arr[] = new InvoiceResource($invoice);
        }

        $resToCamelCase = new CamelConverter();

        $convertedResponse = $resToCamelCase->convert($response->json());

        $response->assertSimilarJson($convertedResponse, $arr);
    }

    public function testStoreMethod()
    {
        $response = $this->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $this->body);

        $response->assertStatus(200);

        $res = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $res);
        $this->assertEquals($this->body['customer_id'], $res['customerId']);
        $this->assertEquals($this->body['currency'], $res['currency']);
        $this->assertArrayHasKey('lines', $res);
        $this->assertCount(1, $res['lines']);
        $this->assertArrayHasKey('quantity', $res['lines'][0]);
        $this->assertArrayHasKey('priceEach', $res['lines'][0]);
        $this->assertArrayHasKey('taxRate', $res['lines'][0]);
        $this->assertArrayHasKey('unit', $res['lines'][0]);
        $this->assertArrayHasKey('detail', $res['lines'][0]);
        $this->assertArrayHasKey('detailPlus', $res['lines'][0]);
    }

    public function testGetInvoiceMethod()
    {
        $response = $this->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $this->body);
        $response->assertStatus(200);
        $res = json_decode($response->getContent(), true);

        $response = $this->get('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices/'.$res['id']);

        $response->assertSimilarJson($res);
    }

    public function testStoreOpenInvoice()
    {
        $numberOfInvoices = Invoice::count();
        $numberOfLineItems = LineItem::count();

        $invoice = [
            'customer_id' => $this->customer->id,
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
            'status' => Invoice::STATUS_OPEN,
            'open_at' => today()->addDays($this->faker->biasedNumberBetween())->toDateString(),
            'days_till_due' => $this->faker->randomElement(Invoice::DAYS_TILL_DUE),
            'performed_when' => $this->faker->text(40),
        ];

        $lineItem = [
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
        ];

        $body = [
            'lines' => [
                $lineItem, $lineItem,
            ],
        ];

        $body = array_merge($body, $invoice);

        $response = $this->actingAs($this->user)->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $body);

        $response->assertStatus(200);

        $this->assertDatabaseCount('invoices', $numberOfInvoices + 1);
        $this->assertDatabaseCount('line_items', $numberOfLineItems + 2);
    }

    public function testStoreInvoiceWithDefaults()
    {
        $invoice = [
            'customer_id' => $this->customer->id,
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
        ];

        $lineItem = [
            'quantity' => 231345,
            'price_each' => 34,
            'tax_rate' => 0.07,
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
        ];

        $body = [
            'lines' => [
                $lineItem,
            ],
        ];

        $numberOfLineItems = LineItem::count();

        $body = array_merge($invoice, $body);

        $response = $this->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $body);

        $response->assertStatus(200);

        $dbInvoice = [
            'status' => Invoice::STATUS_DRAFT,
        ];

        $dbInvoice = array_merge($dbInvoice, $invoice);

        $this->assertDatabaseHas('invoices', $dbInvoice);
        $this->assertDatabaseCount('line_items', $numberOfLineItems + 1);
    }

    public function testStoreInvoiceWithDefaultsWithoutLineItem()
    {
        $numberOfInvoices = Invoice::count();
        $numberOfLineItems = LineItem::count();

        $invoice = [
            'customer_id' => $this->customer->id,
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
        ];

        $body = [];

        $body = array_merge($body, $invoice);

        $response = $this->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $body);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('lines');

        $this->assertDatabaseCount('invoices', $numberOfInvoices + 0);
        $this->assertDatabaseCount('line_items', $numberOfLineItems + 0);
    }

    public function testCorrectlyStoreDraftInvoice()
    {
        $numberOfInvoices = Invoice::count();
        $numberOfLineItems = LineItem::count();

        $invoice = [
            'customer_id' => $this->customer->id,
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
            'status' => Invoice::STATUS_DRAFT,
        ];

        $lineItem = [
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
        ];

        $body = [
            'lines' => [
                $lineItem, $lineItem,
            ],
        ];

        $body = array_merge($body, $invoice);

        $response = $this->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $body);

        $response->assertStatus(200);

        $this->assertDatabaseCount('invoices', $numberOfInvoices + 1);
        $this->assertDatabaseCount('line_items', $numberOfLineItems + 2);
    }

    public function testStoreDraftInvoiceWithOpenFields()
    {
        $numberOfInvoices = Invoice::count();
        $numberOfLineItems = LineItem::count();

        $invoice = [
            'customer_id' => $this->customer->id,
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
            'status' => Invoice::STATUS_DRAFT,
            'open_at' => today()->addDays($this->faker->biasedNumberBetween())->toDateString(),
            'days_till_due' => $this->faker->randomElement(Invoice::DAYS_TILL_DUE),
            'performed_when' => $this->faker->text(40),

        ];

        $lineItem = [
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
        ];

        $body = [
            'lines' => [
                $lineItem, $lineItem,
            ],
        ];

        $body = array_merge($body, $invoice);

        $response = $this->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $body);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['openAt', 'daysTillDue']);

        $this->assertDatabaseCount('invoices', $numberOfInvoices + 0);
        $this->assertDatabaseCount('line_items', $numberOfLineItems + 0);
    }

    public function testStoreOpenInvoiceWithoutOpenFields()
    {
        $numberOfInvoices = Invoice::count();
        $numberOfLineItems = LineItem::count();

        $invoice = [
            'customer_id' => $this->customer->id,
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
            'status' => Invoice::STATUS_OPEN,
        ];

        $lineItem = [
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
        ];

        $body = [
            'lines' => [
                $lineItem, $lineItem,
            ],
        ];

        $body = array_merge($body, $invoice);

        $response = $this->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $body);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['openAt', 'daysTillDue', 'performedWhen']);

        $this->assertDatabaseCount('invoices', $numberOfInvoices + 0);
        $this->assertDatabaseCount('line_items', $numberOfLineItems + 0);
    }

    public function testStoreInvoiceWForDifferentTenantCustomer()
    {
        $tenant = Tenant::factory()->create();
        $customer = Customer::factory()->for($tenant)->create();
        $numberOfInvoices = Invoice::count();
        $numberOfLineItems = LineItem::count();

        $invoice = [
            'customer_id' => $customer->id,
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
            'status' => Invoice::STATUS_DRAFT,
        ];

        $lineItem = [
            'quantity' => '10.11',
            'price_each' => '10023.12',
            'tax_rate' => '0.19',
            'unit' => UnitCode::Piece->value,
            'detail' => 'Website gestalten',
            'detail_plus' => 'Website wurde gestaltet',
        ];

        $body = [
            'lines' => [
                $lineItem, $lineItem,
            ],
        ];

        $body = array_merge($body, $invoice);

        $response = $this->postJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices', $body);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('customerId');

        $this->assertDatabaseCount('invoices', $numberOfInvoices + 0);
        $this->assertDatabaseCount('line_items', $numberOfLineItems + 0);
    }

    public function testShow()
    {
        $invoice = Invoice::factory()->for($this->customer)->create();
        $response = $this->actingAs($this->user)->getJson('http://localhost:8080/api/v2/tenant/'.$this->tenant->id.'/invoices/'.$invoice->id);

        $response->assertStatus(200);
    }
}
