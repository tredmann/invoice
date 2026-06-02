<?php

namespace Tests\Feature\Database\Seeders;

use Database\Seeders\Demo\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_runs_without_error(): void
    {
        $this->seed(DemoSeeder::class);
        $this->assertTrue(true);
    }

    public function test_kranich_tenant_has_profile_and_two_users(): void
    {
        $this->seed(\Database\Seeders\Demo\DemoSeeder::class);

        $tenant = \App\Models\Tenant\Tenant::where('name', 'Kranich Software GmbH')->first();

        $this->assertNotNull($tenant, 'Kranich tenant should exist');
        $this->assertSame('Reichenberger Straße 124', $tenant->currentGeneralInfo->street);
        $this->assertSame('DE123456789', $tenant->currentLegalInfo->vat_no);
        $this->assertCount(2, $tenant->users, 'Kranich should have exactly 2 users');

        $this->assertNotNull(
            \App\Models\User::where('email', 'anna@kranich-software.example')->first(),
            'Anna Kranich should exist as a user'
        );
        $this->assertNotNull(
            \App\Models\User::where('email', 'lukas@kranich-software.example')->first(),
            'Lukas Berger should exist as a user'
        );
    }

    public function test_kranich_has_six_customers_and_eight_mail_receivers(): void
    {
        $this->seed(\Database\Seeders\Demo\DemoSeeder::class);

        $tenant = \App\Models\Tenant\Tenant::where('name', 'Kranich Software GmbH')->firstOrFail();

        $this->assertCount(6, $tenant->customers, 'Kranich should have 6 customers');

        $expectedNames = [
            'Müller Maschinenbau GmbH',
            'Pixelblut Design GbR',
            'Holzbau Eichenhain',
            'Verein für Stadtkultur e.V.',
            'Studio Bellini S.r.l.',
            'Acme Robotics Inc.',
        ];
        foreach ($expectedNames as $name) {
            $this->assertNotNull(
                $tenant->customers->firstWhere('company', $name) ?? $tenant->customers->firstWhere('name', $name),
                "Customer '{$name}' should exist"
            );
        }

        $totalReceivers = \App\Models\CustomerMailReceiver::whereIn(
            'customer_id',
            $tenant->customers->pluck('id')
        )->count();
        $this->assertSame(8, $totalReceivers, 'Kranich customers should have 8 mail receivers total (2+1+1+1+1+2)');
    }

    public function test_kranich_has_sixteen_invoices_covering_all_statuses_and_mail_statuses(): void
    {
        $this->seed(\Database\Seeders\Demo\DemoSeeder::class);

        $tenant = \App\Models\Tenant\Tenant::where('name', 'Kranich Software GmbH')->firstOrFail();
        $customerIds = $tenant->customers->pluck('id');
        $invoices = \App\Models\Invoice::whereIn('customer_id', $customerIds)->get();

        $this->assertCount(16, $invoices, 'Kranich should have 16 invoices');

        $statusesPresent = $invoices->pluck('status')->unique()->values()->all();
        sort($statusesPresent);
        $expected = [
            \App\Models\Invoice::STATUS_CANCELLATION_INVOICE,
            \App\Models\Invoice::STATUS_CANCELLED,
            \App\Models\Invoice::STATUS_DRAFT,
            \App\Models\Invoice::STATUS_OPEN,
            \App\Models\Invoice::STATUS_OPEN_PDF_ERROR,
            \App\Models\Invoice::STATUS_OVERDUE,
            \App\Models\Invoice::STATUS_PAID,
        ];
        sort($expected);
        $this->assertSame($expected, $statusesPresent, 'All 7 invoice statuses should appear');

        $mailStatusesPresent = $invoices->pluck('mail_status')->unique()->values()->all();
        sort($mailStatusesPresent);
        $expectedMail = [
            \App\Models\Invoice::MAIL_STATUS_ERROR,
            \App\Models\Invoice::MAIL_STATUS_MAILABLE,
            \App\Models\Invoice::MAIL_STATUS_MAILED,
            \App\Models\Invoice::MAIL_STATUS_MAILING,
            \App\Models\Invoice::MAIL_STATUS_NOT_MAILABLE,
        ];
        sort($expectedMail);
        $this->assertSame($expectedMail, $mailStatusesPresent, 'All 5 mail statuses should appear');

        $cancellation = $invoices->firstWhere('status', \App\Models\Invoice::STATUS_CANCELLATION_INVOICE);
        $this->assertNotNull($cancellation, 'Cancellation invoice should exist');
        $this->assertNotNull($cancellation->cancelled_invoice_id, 'Cancellation invoice should link to original');
    }

    public function test_kranich_has_six_recurring_invoices_covering_all_frequencies_and_statuses(): void
    {
        $this->seed(\Database\Seeders\Demo\DemoSeeder::class);

        $tenant = \App\Models\Tenant\Tenant::where('name', 'Kranich Software GmbH')->firstOrFail();
        $customerIds = $tenant->customers->pluck('id');
        $masters = \App\Models\MasterInvoice::whereIn('customer_id', $customerIds)->get();

        $this->assertCount(6, $masters, 'Kranich should have 6 recurring invoices');

        $frequencies = $masters->pluck('billing_frequency')->unique()->values()->all();
        sort($frequencies);
        $expectedFreq = ['1 day', '1 month', '1 week', '3 months', '6 months'];
        sort($expectedFreq);
        $this->assertSame($expectedFreq, $frequencies, 'All 5 billing frequencies should appear');

        $statuses = $masters->pluck('status')->unique()->values()->all();
        sort($statuses);
        $expectedStatus = [
            \App\Models\MasterInvoice::STATUS_ACTIVE,
            \App\Models\MasterInvoice::STATUS_DRAFT,
            \App\Models\MasterInvoice::STATUS_PAUSED,
        ];
        sort($expectedStatus);
        $this->assertSame($expectedStatus, $statuses, 'All 3 recurring statuses should appear');
    }

    public function test_northwind_tenant_matches_spec(): void
    {
        $this->seed(\Database\Seeders\Demo\DemoSeeder::class);

        $tenant = \App\Models\Tenant\Tenant::where('name', 'Northwind Creative LLC')->firstOrFail();

        $this->assertCount(1, $tenant->users, 'Northwind should have exactly 1 user (Maya)');
        $this->assertCount(4, $tenant->customers, 'Northwind should have 4 customers');

        $customerIds = $tenant->customers->pluck('id');
        $invoices = \App\Models\Invoice::whereIn('customer_id', $customerIds)->get();
        $masters = \App\Models\MasterInvoice::whereIn('customer_id', $customerIds)->get();

        $this->assertCount(8, $invoices, 'Northwind should have 8 invoices');
        $this->assertCount(2, $masters, 'Northwind should have 2 recurring invoices');

        $this->assertTrue(
            $invoices->every(fn ($i) => $i->currency === 'USD'),
            'All Northwind invoices should be USD'
        );
    }

    public function test_full_database_seeder_produces_expected_totals(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->assertSame(2, \App\Models\Tenant\Tenant::count(), '2 tenants');
        $this->assertSame(4, \App\Models\User::count(), '1 admin + 2 Kranich + 1 Northwind = 4 users');
        $this->assertSame(10, \App\Models\Customer::count(), '6 Kranich + 4 Northwind = 10 customers');
        $this->assertSame(24, \App\Models\Invoice::count(), '16 Kranich + 8 Northwind = 24 invoices');
        $this->assertSame(8, \App\Models\MasterInvoice::count(), '6 Kranich + 2 Northwind = 8 recurring');
    }
}
