<?php

namespace Database\Seeders\Demo;

use App\Models\Customer;
use App\Models\CustomerMailReceiver;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\MasterInvoice;
use App\Models\MasterLineItem;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\LegalInfo;
use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Services\Invoices\InvoiceService;
use App\Services\MasterInvoices\MasterInvoiceService;
use Carbon\Carbon;
use Database\Seeders\Demo\Concerns\SeedsMailpitSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NorthwindCreativeSeeder extends Seeder
{
    use SeedsMailpitSettings;

    private string $demoPassword;

    public function run(): void
    {
        $this->demoPassword = env('SEED_DEMO_PASSWORD') ?: Str::random(16);

        $owner = User::create([
            'name' => 'Maya Patel',
            'email' => 'maya@northwindcreative.example',
            'email_verified_at' => now(),
            'password' => Hash::make($this->demoPassword),
            'remember_token' => Str::random(10),
        ]);

        $tenant = $this->seedTenant($owner);
        $this->seedMailpitSettings($tenant, 'hello@northwindcreative.example');
        $this->seedCustomers($tenant, $owner);
        $this->seedInvoices($tenant, $owner);
        $this->seedRecurring($tenant, $owner);

        $this->command->info('Northwind Creative LLC seeded.');
        $this->command->info('  Owner login: maya@northwindcreative.example');
        if (! env('SEED_DEMO_PASSWORD')) {
            $this->command->warn('  Generated demo password (Maya): '.$this->demoPassword);
        }
    }

    private function seedTenant(User $owner): Tenant
    {
        $generalInfo = GeneralInfo::create([
            'name' => 'Northwind Creative LLC',
            'owner' => 'Maya Patel',
            'street' => '487 Bedford Avenue, Suite 3B',
            'postal' => '11211',
            'city' => 'Brooklyn',
            'country' => 'US',
            'email' => 'hello@northwindcreative.example',
            'homepage' => 'northwindcreative.example',
        ]);

        // LegalInfo is German-centric; US identifiers go into the closest fields.
        // See spec: docs/superpowers/specs/2026-06-01-demo-seeder-design.md "Known limitations".
        $legalInfo = LegalInfo::create([
            'registry_court' => 'State of New York',
            'registry_no' => 'NY-LLC-2022-87654',
            'company_owner' => 'Maya Patel',
            'tax_no' => '12-3456789',
            'vat_no' => '',
            'swift_bic' => '',
            'iban' => 'CHASE-NY-1234567890',
            'bank_institute' => 'Chase Bank',
        ]);

        $tenant = Tenant::create([
            'name' => 'Northwind Creative LLC',
            'slug' => Str::slug('Northwind Creative LLC'),
            'owner_id' => $owner->id,
            'general_info_id' => $generalInfo->id,
            'legal_info_id' => $legalInfo->id,
        ]);

        $tenant->users()->attach($owner);

        return $tenant;
    }

    private function seedCustomers(Tenant $tenant, User $owner): void
    {
        $definitions = [
            [
                'company' => 'Sunfire Coffee Co.',
                'name' => 'Jordan Reyes',
                'street' => '212 Bedford Avenue',
                'postal' => '11249',
                'city' => 'Brooklyn',
                'receivers' => [['email' => 'jordan@sunfirecoffee.example', 'first_name' => 'Jordan', 'last_name' => 'Reyes']],
            ],
            [
                'company' => 'Linden & Quill, LLC',
                'name' => 'Eleanor Quill',
                'street' => '85 Warren Street',
                'postal' => '12534',
                'city' => 'Hudson',
                'receivers' => [['email' => 'eleanor@lindenquill.example', 'first_name' => 'Eleanor', 'last_name' => 'Quill']],
            ],
            [
                'company' => 'Halcyon Yoga Studio',
                'name' => 'Priya Iyer',
                'street' => '188 Smith Street',
                'postal' => '11201',
                'city' => 'Brooklyn',
                'receivers' => [['email' => 'priya@halcyonyoga.example', 'first_name' => 'Priya', 'last_name' => 'Iyer']],
            ],
            [
                'company' => 'Maple Leaf Marketing',
                'name' => 'Owen Tremblay',
                'street' => '720 King Street West',
                'postal' => 'M5V 2T3',
                'city' => 'Toronto',
                'receivers' => [['email' => 'owen@mapleleafmkt.example', 'first_name' => 'Owen', 'last_name' => 'Tremblay']],
            ],
        ];

        foreach ($definitions as $def) {
            $customer = Customer::create([
                'tenant_id' => $tenant->id,
                'user_id' => $owner->id,
                'company' => $def['company'],
                'name' => $def['name'],
                'street' => $def['street'],
                'postal' => $def['postal'],
                'city' => $def['city'],
            ]);

            foreach ($def['receivers'] as $receiver) {
                CustomerMailReceiver::create([
                    'user_id' => $owner->id,
                    'customer_id' => $customer->id,
                    'email' => $receiver['email'],
                    'gender' => CustomerMailReceiver::DIVERSE,
                    'first_name' => $receiver['first_name'],
                    'last_name' => $receiver['last_name'],
                ]);
            }
        }
    }

    private function seedInvoices(Tenant $tenant, User $owner): void
    {
        $customers = $tenant->customers->keyBy('company');

        $definitions = [
            // 1. Sunfire Coffee — Draft
            ['state' => 'draft', 'customer' => 'Sunfire Coffee Co.',
             'line_items' => [['quantity' => 1, 'unit' => 'project', 'price_each' => 350000, 'tax_rate' => 0, 'detail' => 'Brand refresh proposal']]],
            // 2. Sunfire Coffee — Paid
            ['state' => 'paid', 'customer' => 'Sunfire Coffee Co.', 'mail_status' => Invoice::MAIL_STATUS_MAILED,
             'opened_days_ago' => 45, 'days_till_due' => 14, 'paid_days_ago' => 28, 'performed_when' => 'April 2026',
             'line_items' => [['quantity' => 1, 'unit' => 'project', 'price_each' => 280000, 'tax_rate' => 0, 'detail' => 'Logo redesign and brand mark']]],
            // 3. Linden & Quill — Open (3 line items)
            ['state' => 'open', 'customer' => 'Linden & Quill, LLC', 'mail_status' => Invoice::MAIL_STATUS_MAILED,
             'opened_days_ago' => 6, 'days_till_due' => 14, 'performed_when' => 'May 2026',
             'line_items' => [
                 ['quantity' => 1, 'unit' => 'cover', 'price_each' => 120000, 'tax_rate' => 0, 'detail' => 'Cover design — "The Long Quiet"'],
                 ['quantity' => 1, 'unit' => 'cover', 'price_each' => 120000, 'tax_rate' => 0, 'detail' => 'Cover design — "Northbound"'],
                 ['quantity' => 1, 'unit' => 'cover', 'price_each' => 120000, 'tax_rate' => 0, 'detail' => 'Cover design — "Field Notes"'],
             ]],
            // 4. Halcyon Yoga Studio — Open
            ['state' => 'open', 'customer' => 'Halcyon Yoga Studio', 'mail_status' => Invoice::MAIL_STATUS_MAILED,
             'opened_days_ago' => 10, 'days_till_due' => 14, 'performed_when' => 'May 2026',
             'line_items' => [['quantity' => 1, 'unit' => 'project', 'price_each' => 220000, 'tax_rate' => 0, 'detail' => 'Web design — Halcyon Yoga Studio landing page']]],
            // 5. Halcyon Yoga Studio — Overdue
            ['state' => 'overdue', 'customer' => 'Halcyon Yoga Studio', 'mail_status' => Invoice::MAIL_STATUS_MAILED,
             'opened_days_ago' => 28, 'days_till_due' => 14, 'performed_when' => 'April 2026',
             'line_items' => [['quantity' => 1, 'unit' => 'project', 'price_each' => 95000, 'tax_rate' => 0, 'detail' => 'Brand guidelines document']]],
            // 6. Maple Leaf Marketing — Open, cross-border
            ['state' => 'open', 'customer' => 'Maple Leaf Marketing', 'mail_status' => Invoice::MAIL_STATUS_MAILABLE,
             'opened_days_ago' => 3, 'days_till_due' => 14, 'performed_when' => 'May 2026',
             'line_items' => [['quantity' => 1, 'unit' => 'project', 'price_each' => 180000, 'tax_rate' => 0, 'detail' => 'Illustration project (set of 6)']]],
            // 7. Linden & Quill — Cancelled
            ['state' => 'cancelled', 'customer' => 'Linden & Quill, LLC',
             'opened_days_ago' => 40, 'days_till_due' => 14, 'performed_when' => 'April 2026',
             'line_items' => [['quantity' => 1, 'unit' => 'project', 'price_each' => 240000, 'tax_rate' => 0, 'detail' => 'Cover series (cancelled, project scope changed)']]],
            // 8. Linden & Quill — Cancellation Invoice (paired with 7)
            ['state' => 'cancellation_invoice', 'customer' => 'Linden & Quill, LLC',
             'cancelled_pair_with' => 6, // zero-indexed: definition #7 is index 6
             'opened_days_ago' => 38, 'days_till_due' => 14, 'performed_when' => 'April 2026 (cancellation)',
             'line_items' => [['quantity' => 1, 'unit' => 'project', 'price_each' => 240000, 'tax_rate' => 0, 'detail' => 'Cancellation — Cover series']]],
        ];

        $createdInvoices = [];
        foreach ($definitions as $idx => $def) {
            $createdInvoices[$idx] = $this->createInvoice($def, $customers, $owner, $createdInvoices);
        }
    }

    private function createInvoice(
        array $def,
        \Illuminate\Support\Collection $customers,
        User $owner,
        array $createdInvoices
    ): Invoice {
        $customer = $customers->get($def['customer']);
        if ($customer === null) {
            throw new \RuntimeException("Customer '{$def['customer']}' not found in seeded data");
        }

        $factory = Invoice::factory()->state([
            'customer_id' => $customer->id,
            'user_id' => $owner->id,
            'currency' => 'USD',
            'performed_when' => $def['performed_when'] ?? null,
        ]);

        switch ($def['state']) {
            case 'draft':
                break;
            case 'open':
                $factory = $factory->open()->state($this->dateOverridesForOpen($def));
                break;
            case 'paid':
                $factory = $factory->open()->paid()->state($this->dateOverridesForPaid($def));
                break;
            case 'overdue':
                $factory = $factory->open()->overdue()->state($this->dateOverridesForOpen($def));
                break;
            case 'pdf_error':
                $factory = $factory->open()->pdfError()->state($this->dateOverridesForOpen($def));
                break;
            case 'cancelled':
                $factory = $factory->open()->cancelled()->state($this->dateOverridesForOpen($def));
                break;
            case 'cancellation_invoice':
                $original = $createdInvoices[$def['cancelled_pair_with']];
                $factory = $factory->cancellationInvoice($original)->state($this->dateOverridesForOpen($def));
                break;
        }

        if (! empty($def['mail_status'])) {
            $factory = $factory->withMailStatus($def['mail_status']);
        }

        $invoice = $factory->create();

        // Cancellation invoices proxy their lineItems() to the cancelled invoice's items
        // (see Invoice model). Creating new LineItem rows here would be orphaned.
        // Skip line item creation for cancellation_invoice state.
        if ($def['state'] !== 'cancellation_invoice') {
            foreach ($def['line_items'] as $item) {
                $without = (int) round($item['quantity'] * $item['price_each']);
                $with = (int) round($without * (1 + $item['tax_rate']));
                LineItem::create([
                    'user_id' => $owner->id,
                    'invoice_id' => $invoice->id,
                    'quantity' => $item['quantity'],
                    'price_each' => $item['price_each'],
                    'currency' => 'USD',
                    'without_tax' => $without,
                    'tax_rate' => $item['tax_rate'],
                    'with_tax' => $with,
                    'unit' => $item['unit'],
                    'detail' => $item['detail'],
                ]);
            }

            InvoiceService::totalsUpdate($invoice);
        }

        return $invoice;
    }

    private function dateOverridesForOpen(array $def): array
    {
        $opened = Carbon::now()->subDays($def['opened_days_ago']);
        return [
            'open_at' => $opened,
            'days_till_due' => $def['days_till_due'],
            'date_due' => $opened->copy()->addDays($def['days_till_due']),
        ];
    }

    private function dateOverridesForPaid(array $def): array
    {
        return array_merge(
            $this->dateOverridesForOpen($def),
            ['paid_at' => Carbon::now()->subDays($def['paid_days_ago'])]
        );
    }

    private function seedRecurring(Tenant $tenant, User $owner): void
    {
        $customers = $tenant->customers->keyBy('company');

        $definitions = [
            ['customer' => 'Sunfire Coffee Co.', 'status' => 'active', 'frequency' => '1 month', 'next_print_days' => 10, 'days_till_due' => 14,
             'line_items' => [['quantity' => 1, 'unit' => 'month', 'price_each' => 90000, 'tax_rate' => 0, 'detail' => 'Monthly social-media graphics retainer']]],
            ['customer' => 'Halcyon Yoga Studio', 'status' => 'paused', 'frequency' => '3 months', 'next_print_days' => null, 'days_till_due' => 14,
             'line_items' => [['quantity' => 1, 'unit' => 'quarter', 'price_each' => 175000, 'tax_rate' => 0, 'detail' => 'Quarterly seasonal campaign (paused)']]],
        ];

        foreach ($definitions as $def) {
            $customer = $customers->get($def['customer']);
            $factory = MasterInvoice::factory()->state([
                'customer_id' => $customer->id,
                'user_id' => $owner->id,
                'currency' => 'USD',
                'days_till_due' => $def['days_till_due'],
            ]);

            switch ($def['status']) {
                case 'active':
                    $factory = $factory->active()->state([
                        'billing_frequency' => $def['frequency'],
                        'next_print' => Carbon::now()->addDays($def['next_print_days'])->toDateString(),
                    ]);
                    break;
                case 'paused':
                    $factory = $factory->paused()->state([
                        'billing_frequency' => $def['frequency'],
                    ]);
                    break;
            }

            $master = $factory->create();

            foreach ($def['line_items'] as $item) {
                $without = (int) round($item['quantity'] * $item['price_each']);
                $with = (int) round($without * (1 + $item['tax_rate']));
                MasterLineItem::create([
                    'master_invoice_id' => $master->id,
                    'user_id' => $owner->id,
                    'quantity' => $item['quantity'],
                    'price_each' => $item['price_each'],
                    'currency' => 'USD',
                    'without_tax' => $without,
                    'tax_rate' => $item['tax_rate'],
                    'with_tax' => $with,
                    'unit' => $item['unit'],
                    'detail' => $item['detail'],
                ]);
            }

            MasterInvoiceService::totalsUpdate($master);
        }
    }
}
