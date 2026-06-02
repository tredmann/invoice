<?php

namespace Database\Seeders\Demo;

use App\Models\Customer;
use App\Models\CustomerMailReceiver;
use App\Models\Invoice;
use App\Models\InvoiceDocument;
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

class KranichSoftwareSeeder extends Seeder
{
    use SeedsMailpitSettings;

    private string $demoPassword;

    public function run(): void
    {
        $this->demoPassword = env('SEED_DEMO_PASSWORD') ?: Str::random(16);

        $owner = $this->seedOwner();
        $tenant = $this->seedTenant($owner);
        $this->seedMailpitSettings($tenant, 'hallo@kranich-software.example');
        $this->seedAdditionalUsers($tenant);
        $this->seedCustomers($tenant, $owner);
        $this->seedInvoices($tenant, $owner);
        $this->seedRecurring($tenant, $owner);

        $this->command->info('Kranich Software GmbH seeded.');
        $this->command->info('  Owner login: anna@kranich-software.example');
        if (! env('SEED_DEMO_PASSWORD')) {
            $this->command->warn('  Generated demo password (Anna + Lukas): '.$this->demoPassword);
        }
    }

    private function seedOwner(): User
    {
        return User::create([
            'name' => 'Anna Kranich',
            'email' => 'anna@kranich-software.example',
            'email_verified_at' => now(),
            'password' => Hash::make($this->demoPassword),
            'remember_token' => Str::random(10),
        ]);
    }

    private function seedTenant(User $owner): Tenant
    {
        $generalInfo = GeneralInfo::create([
            'name' => 'Kranich Software GmbH',
            'owner' => 'Anna Kranich',
            'street' => 'Reichenberger Straße 124',
            'postal' => '10999',
            'city' => 'Berlin',
            'country' => 'DE',
            'email' => 'hallo@kranich-software.example',
            'homepage' => 'kranich-software.example',
        ]);

        $legalInfo = LegalInfo::create([
            'registry_court' => 'Amtsgericht Berlin-Charlottenburg',
            'registry_no' => 'HRB 234567 B',
            'company_owner' => 'Anna Kranich',
            'tax_no' => '27/123/45678',
            'vat_no' => 'DE123456789',
            'swift_bic' => 'COBADEFFXXX',
            'iban' => 'DE89 3704 0044 0532 0130 00',
            'bank_institute' => 'Commerzbank Berlin',
        ]);

        $tenant = Tenant::create([
            'name' => 'Kranich Software GmbH',
            'slug' => Str::slug('Kranich Software GmbH'),
            'owner_id' => $owner->id,
            'general_info_id' => $generalInfo->id,
            'legal_info_id' => $legalInfo->id,
        ]);

        $tenant->users()->attach($owner);

        return $tenant;
    }

    private function seedAdditionalUsers(Tenant $tenant): void
    {
        $lukas = User::create([
            'name' => 'Lukas Berger',
            'email' => 'lukas@kranich-software.example',
            'email_verified_at' => now(),
            'password' => Hash::make($this->demoPassword),
            'remember_token' => Str::random(10),
        ]);

        $tenant->users()->attach($lukas);
    }

    private function seedCustomers(Tenant $tenant, User $owner): void
    {
        $definitions = [
            [
                'company' => 'Müller Maschinenbau GmbH',
                'name' => 'Friedrich Müller',
                'street' => 'Industriestraße 12',
                'postal' => '70565',
                'city' => 'Stuttgart',
                'receivers' => [
                    ['email' => 'rechnung@mueller-maschinenbau.example', 'first_name' => 'Buchhaltung', 'last_name' => 'Müller'],
                    ['email' => 'buchhaltung@mueller-maschinenbau.example', 'first_name' => 'Inge', 'last_name' => 'Hahn'],
                ],
            ],
            [
                'company' => 'Pixelblut Design GbR',
                'name' => 'Jana Voss',
                'street' => 'Oranienstraße 198',
                'postal' => '10999',
                'city' => 'Berlin',
                'receivers' => [
                    ['email' => 'jana@pixelblut.example', 'first_name' => 'Jana', 'last_name' => 'Voss'],
                ],
            ],
            [
                'company' => 'Holzbau Eichenhain',
                'name' => 'Stefan Eichenhain',
                'street' => 'Dorfstraße 5',
                'postal' => '16321',
                'city' => 'Bernau',
                'receivers' => [
                    ['email' => 'stefan@holzbau-eichenhain.example', 'first_name' => 'Stefan', 'last_name' => 'Eichenhain'],
                ],
            ],
            [
                'company' => 'Verein für Stadtkultur e.V.',
                'name' => 'Dr. Carla Brandt',
                'street' => 'Schönhauser Allee 81',
                'postal' => '10439',
                'city' => 'Berlin',
                'receivers' => [
                    ['email' => 'vorstand@stadtkultur-verein.example', 'first_name' => 'Carla', 'last_name' => 'Brandt'],
                ],
            ],
            [
                'company' => 'Studio Bellini S.r.l.',
                'name' => 'Marco Bellini',
                'street' => 'Via dei Coronari 45',
                'postal' => '00186',
                'city' => 'Roma',
                'receivers' => [
                    ['email' => 'marco@studiobellini.example', 'first_name' => 'Marco', 'last_name' => 'Bellini'],
                ],
            ],
            [
                'company' => 'Acme Robotics Inc.',
                'name' => 'Patricia Chen',
                'street' => '1200 Industrial Way',
                'postal' => '95110',
                'city' => 'San Jose',
                'receivers' => [
                    ['email' => 'ap@acmerobotics.example', 'first_name' => 'Patricia', 'last_name' => 'Chen'],
                    ['email' => 'finance@acmerobotics.example', 'first_name' => 'Dale', 'last_name' => 'Yost'],
                ],
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

        $definitions = $this->invoiceDefinitions();

        $createdInvoices = [];
        foreach ($definitions as $idx => $def) {
            $createdInvoices[$idx] = $this->createInvoice($def, $customers, $owner, $createdInvoices);
        }
    }

    private function createInvoice(
        array $def,
        \Illuminate\Support\Collection $customers,
        User $owner,
        array &$createdInvoices
    ): Invoice {
        $customer = $customers->get($def['customer']);
        if ($customer === null) {
            throw new \RuntimeException("Customer '{$def['customer']}' not found in seeded data");
        }

        $factory = Invoice::factory()->state([
            'customer_id' => $customer->id,
            'user_id' => $owner->id,
            'currency' => 'EUR',
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
        // (see Invoice model). Creating new LineItem rows here would be permanently orphaned.
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
                    'currency' => 'EUR',
                    'without_tax' => $without,
                    'tax_rate' => $item['tax_rate'],
                    'with_tax' => $with,
                    'unit' => $item['unit'],
                    'detail' => $item['detail'],
                    'detail_plus' => $item['detail_plus'] ?? null,
                ]);
            }

            InvoiceService::totalsUpdate($invoice);
        }

        foreach ($def['attachments'] ?? [] as $filename) {
            InvoiceDocument::create([
                'user_id' => $owner->id,
                'invoice_id' => $invoice->id,
                'path' => 'seed/kranich/'.$filename,
                'mime_type' => 'application/pdf',
                'storage' => 'local',
                'type' => InvoiceDocument::TYPE_ATTACHMENT,
            ]);
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
            'performed_when' => $def['performed_when'] ?? null,
        ];
    }

    private function dateOverridesForPaid(array $def): array
    {
        return array_merge(
            $this->dateOverridesForOpen($def),
            ['paid_at' => Carbon::now()->subDays($def['paid_days_ago'])]
        );
    }

    /** @return array<int,array<string,mixed>> */
    private function invoiceDefinitions(): array
    {
        return [
            // 0. Müller Maschinenbau — Draft (3 line items)
            [
                'state' => 'draft',
                'customer' => 'Müller Maschinenbau GmbH',
                'line_items' => [
                    ['quantity' => 12, 'unit' => 'h', 'price_each' => 12000, 'tax_rate' => 0.19, 'detail' => 'Backend-Entwicklung Phase 2'],
                    ['quantity' => 4, 'unit' => 'h', 'price_each' => 12000, 'tax_rate' => 0.19, 'detail' => 'Projektkoordination'],
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 50000, 'tax_rate' => 0.19, 'detail' => 'Architektur-Workshop'],
                ],
            ],
            // 1. Pixelblut Design — Open, mail Sent (+ 1 attachment)
            [
                'state' => 'open',
                'customer' => 'Pixelblut Design GbR',
                'mail_status' => Invoice::MAIL_STATUS_MAILED,
                'opened_days_ago' => 5,
                'days_till_due' => 14,
                'performed_when' => 'Juni 2026',
                'line_items' => [
                    ['quantity' => 8, 'unit' => 'h', 'price_each' => 11000, 'tax_rate' => 0.19, 'detail' => 'CMS-Migration Beratung'],
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 80000, 'tax_rate' => 0.19, 'detail' => 'Datenmigration'],
                ],
                'attachments' => ['project_report.pdf'],
            ],
            // 2. Holzbau Eichenhain — Paid (Kleinunternehmer 0%)
            [
                'state' => 'paid',
                'customer' => 'Holzbau Eichenhain',
                'mail_status' => Invoice::MAIL_STATUS_MAILED,
                'opened_days_ago' => 30,
                'days_till_due' => 7,
                'paid_days_ago' => 14,
                'performed_when' => 'Mai 2026',
                'line_items' => [
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 180000, 'tax_rate' => 0, 'detail' => 'Webseiten-Relaunch'],
                ],
            ],
            // 3. Verein für Stadtkultur — Heavily Overdue (7% reduced)
            [
                'state' => 'overdue',
                'customer' => 'Verein für Stadtkultur e.V.',
                'mail_status' => Invoice::MAIL_STATUS_MAILED,
                'opened_days_ago' => 95,
                'days_till_due' => 14,
                'performed_when' => 'Q1 2026',
                'line_items' => [
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 240000, 'tax_rate' => 0.07, 'detail' => 'Kulturplattform-Wartung Q1 2026'],
                ],
            ],
            // 4. Pixelblut Design — Mildly Overdue, mail failed
            [
                'state' => 'overdue',
                'customer' => 'Pixelblut Design GbR',
                'mail_status' => Invoice::MAIL_STATUS_ERROR,
                'opened_days_ago' => 35,
                'days_till_due' => 14,
                'performed_when' => 'Februar 2026',
                'line_items' => [
                    ['quantity' => 1, 'unit' => 'Monat', 'price_each' => 15000, 'tax_rate' => 0.19, 'detail' => 'Hosting Februar 2026'],
                ],
            ],
            // 5. Müller Maschinenbau — Paid (recurring-generated)
            [
                'state' => 'paid',
                'customer' => 'Müller Maschinenbau GmbH',
                'mail_status' => Invoice::MAIL_STATUS_MAILED,
                'opened_days_ago' => 60,
                'days_till_due' => 30,
                'paid_days_ago' => 45,
                'performed_when' => 'April 2026',
                'line_items' => [
                    ['quantity' => 1, 'unit' => 'Monat', 'price_each' => 300000, 'tax_rate' => 0.19, 'detail' => 'Monatlicher Retainer April 2026'],
                ],
            ],
            // 6. Studio Bellini — Open, EU intra-community (0%, reverse charge)
            [
                'state' => 'open',
                'customer' => 'Studio Bellini S.r.l.',
                'mail_status' => Invoice::MAIL_STATUS_MAILABLE,
                'opened_days_ago' => 7,
                'days_till_due' => 14,
                'performed_when' => 'Mai 2026',
                'line_items' => [
                    ['quantity' => 6, 'unit' => 'h', 'price_each' => 11000, 'tax_rate' => 0, 'detail' => 'Consulting May 2026', 'detail_plus' => 'Reverse charge — VAT to be paid by the recipient (Art. 196 EU VAT Directive).'],
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 40000, 'tax_rate' => 0, 'detail' => 'Reisekosten'],
                ],
            ],
            // 7. Acme Robotics — Open, non-EU export (0%)
            [
                'state' => 'open',
                'customer' => 'Acme Robotics Inc.',
                'mail_status' => Invoice::MAIL_STATUS_MAILING,
                'opened_days_ago' => 10,
                'days_till_due' => 14,
                'performed_when' => 'May 2026',
                'line_items' => [
                    ['quantity' => 16, 'unit' => 'h', 'price_each' => 13000, 'tax_rate' => 0, 'detail' => 'DevOps audit'],
                    ['quantity' => 1, 'unit' => 'fixed', 'price_each' => 150000, 'tax_rate' => 0, 'detail' => 'Final report and recommendations'],
                    ['quantity' => 1, 'unit' => 'fixed', 'price_each' => 80000, 'tax_rate' => 0, 'detail' => 'Knowledge transfer session'],
                ],
            ],
            // 8. Müller Maschinenbau — Cancelled
            [
                'state' => 'cancelled',
                'customer' => 'Müller Maschinenbau GmbH',
                'opened_days_ago' => 65,
                'days_till_due' => 14,
                'performed_when' => 'März 2026',
                'line_items' => [
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 220000, 'tax_rate' => 0.19, 'detail' => 'Initiale Anforderungsaufnahme (storniert wegen Scope-Streit)'],
                ],
            ],
            // 9. Müller Maschinenbau — Cancellation Invoice (paired with index 8)
            [
                'state' => 'cancellation_invoice',
                'customer' => 'Müller Maschinenbau GmbH',
                'cancelled_pair_with' => 8,
                'opened_days_ago' => 60,
                'days_till_due' => 14,
                'performed_when' => 'März 2026 (Storno)',
                'line_items' => [
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 220000, 'tax_rate' => 0.19, 'detail' => 'Stornierung Rechnung K-...'],
                ],
            ],
            // 10. Pixelblut Design — Multi-VAT (19% + 7%)
            [
                'state' => 'open',
                'customer' => 'Pixelblut Design GbR',
                'mail_status' => Invoice::MAIL_STATUS_MAILED,
                'opened_days_ago' => 12,
                'days_till_due' => 14,
                'performed_when' => 'Mai 2026',
                'line_items' => [
                    ['quantity' => 1, 'unit' => 'Tag', 'price_each' => 120000, 'tax_rate' => 0.19, 'detail' => 'Inhouse-Workshop "Modern Laravel"'],
                    ['quantity' => 8, 'unit' => 'Stück', 'price_each' => 1800, 'tax_rate' => 0.07, 'detail' => 'Gedruckte Workshop-Unterlagen'],
                ],
            ],
            // 11. Müller Maschinenbau — PDF Generation Error
            [
                'state' => 'pdf_error',
                'customer' => 'Müller Maschinenbau GmbH',
                'opened_days_ago' => 1,
                'days_till_due' => 14,
                'performed_when' => 'Mai 2026',
                'line_items' => [
                    ['quantity' => 4, 'unit' => 'h', 'price_each' => 12000, 'tax_rate' => 0.19, 'detail' => 'Notfall-Hotfix CI/CD-Pipeline'],
                ],
            ],
            // 12. Holzbau Eichenhain — Open with extended description (detail_plus)
            [
                'state' => 'open',
                'customer' => 'Holzbau Eichenhain',
                'mail_status' => Invoice::MAIL_STATUS_MAILED,
                'opened_days_ago' => 3,
                'days_till_due' => 7,
                'performed_when' => 'Mai 2026',
                'line_items' => [
                    [
                        'quantity' => 1,
                        'unit' => 'Pauschal',
                        'price_each' => 90000,
                        'tax_rate' => 0,
                        'detail' => 'Online-Shop Erweiterung',
                        'detail_plus' => "Umfang dieser Erweiterung:\n- Anbindung an Lexware Warenwirtschaft\n- Neue Produktkategorie 'Sondermaße'\n- Versandkostenrechner für Sperrgut\n- 2 Browser-Tests Chrome + Firefox",
                    ],
                ],
            ],
            // 13. Müller Maschinenbau — Draft with 10 line items
            [
                'state' => 'draft',
                'customer' => 'Müller Maschinenbau GmbH',
                'line_items' => [
                    ['quantity' => 40, 'unit' => 'h', 'price_each' => 12000, 'tax_rate' => 0.19, 'detail' => 'Beratungsleistung Anna Kranich'],
                    ['quantity' => 28, 'unit' => 'h', 'price_each' => 11000, 'tax_rate' => 0.19, 'detail' => 'Entwicklung Lukas Berger'],
                    ['quantity' => 12, 'unit' => 'h', 'price_each' => 12000, 'tax_rate' => 0.19, 'detail' => 'Code Review & QA'],
                    ['quantity' => 4, 'unit' => 'h', 'price_each' => 12000, 'tax_rate' => 0.19, 'detail' => 'Architektur-Sessions'],
                    ['quantity' => 2, 'unit' => 'Tag', 'price_each' => 90000, 'tax_rate' => 0.19, 'detail' => 'Vor-Ort-Termine Stuttgart'],
                    ['quantity' => 4, 'unit' => 'Fahrt', 'price_each' => 18000, 'tax_rate' => 0.19, 'detail' => 'Bahnreisen Berlin–Stuttgart'],
                    ['quantity' => 6, 'unit' => 'Nacht', 'price_each' => 14500, 'tax_rate' => 0.19, 'detail' => 'Hotelübernachtungen'],
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 25000, 'tax_rate' => 0.19, 'detail' => 'Materialien & Hardware'],
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 12000, 'tax_rate' => 0.19, 'detail' => 'Lizenzen Dritter (anteilig)'],
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 5000, 'tax_rate' => 0.19, 'detail' => 'Kleinmaterial / Spesen'],
                ],
            ],
            // 14. Pixelblut Design — Open with 2 attachments
            [
                'state' => 'open',
                'customer' => 'Pixelblut Design GbR',
                'mail_status' => Invoice::MAIL_STATUS_MAILED,
                'opened_days_ago' => 2,
                'days_till_due' => 14,
                'performed_when' => 'Mai 2026',
                'line_items' => [
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 180000, 'tax_rate' => 0.19, 'detail' => 'Projektabschluss "Kundenportal"'],
                    ['quantity' => 4, 'unit' => 'h', 'price_each' => 12000, 'tax_rate' => 0.19, 'detail' => 'Schulung Endnutzer'],
                ],
                'attachments' => ['project_report.pdf', 'time_log.pdf'],
            ],
            // 15. Verein für Stadtkultur — Paid (older, Q4 2025)
            [
                'state' => 'paid',
                'customer' => 'Verein für Stadtkultur e.V.',
                'mail_status' => Invoice::MAIL_STATUS_MAILED,
                'opened_days_ago' => 180,
                'days_till_due' => 14,
                'paid_days_ago' => 160,
                'performed_when' => 'Q4 2025',
                'line_items' => [
                    ['quantity' => 1, 'unit' => 'Pauschal', 'price_each' => 480000, 'tax_rate' => 0.07, 'detail' => 'Kulturplattform initiales Setup'],
                ],
            ],
        ];
    }

    private function seedRecurring(Tenant $tenant, User $owner): void
    {
        $customers = $tenant->customers->keyBy('company');

        $definitions = [
            // MK-1: Müller — Active monthly
            ['customer' => 'Müller Maschinenbau GmbH', 'status' => 'active', 'frequency' => '1 month', 'next_print_days' => 5, 'days_till_due' => 30,
             'line_items' => [['quantity' => 1, 'unit' => 'Monat', 'price_each' => 300000, 'tax_rate' => 0.19, 'detail' => 'Monatlicher Retainer (Backend + DevOps)']]],
            // MK-2: Pixelblut — Active quarterly
            ['customer' => 'Pixelblut Design GbR', 'status' => 'active', 'frequency' => '3 months', 'next_print_days' => 30, 'days_till_due' => 14,
             'line_items' => [['quantity' => 1, 'unit' => 'Quartal', 'price_each' => 600000, 'tax_rate' => 0.19, 'detail' => 'Quartalsweiser Support-Vertrag']]],
            // MK-3: Acme Robotics — Active half-yearly (0%)
            ['customer' => 'Acme Robotics Inc.', 'status' => 'active', 'frequency' => '6 months', 'next_print_days' => 60, 'days_till_due' => 14,
             'line_items' => [['quantity' => 1, 'unit' => 'period', 'price_each' => 1200000, 'tax_rate' => 0, 'detail' => 'Half-yearly DevOps subscription (incl. on-call)']]],
            // MK-4: Stadtkultur — Paused monthly (7%)
            ['customer' => 'Verein für Stadtkultur e.V.', 'status' => 'paused', 'frequency' => '1 month', 'next_print_days' => null, 'days_till_due' => 14,
             'line_items' => [['quantity' => 1, 'unit' => 'Monat', 'price_each' => 80000, 'tax_rate' => 0.07, 'detail' => 'Monatliche Plattformwartung (pausiert)']]],
            // MK-5: Holzbau Eichenhain — Draft weekly (0%)
            ['customer' => 'Holzbau Eichenhain', 'status' => 'draft', 'frequency' => '1 week', 'next_print_days' => null, 'days_till_due' => 7,
             'line_items' => [['quantity' => 1, 'unit' => 'Woche', 'price_each' => 15000, 'tax_rate' => 0, 'detail' => 'Wöchentliche Wartungspauschale']]],
            // MK-6: Studio Bellini — Active daily (0%)
            ['customer' => 'Studio Bellini S.r.l.', 'status' => 'active', 'frequency' => '1 day', 'next_print_days' => 1, 'days_till_due' => 14,
             'line_items' => [['quantity' => 1, 'unit' => 'day', 'price_each' => 5000, 'tax_rate' => 0, 'detail' => 'Daily usage fee (intra-community, reverse charge)']]],
        ];

        foreach ($definitions as $def) {
            $customer = $customers->get($def['customer']);
            if ($customer === null) {
                throw new \RuntimeException("Customer '{$def['customer']}' not found in seeded data");
            }

            $factory = MasterInvoice::factory()->state([
                'customer_id' => $customer->id,
                'user_id' => $owner->id,
                'currency' => 'EUR',
                'days_till_due' => $def['days_till_due'],
            ]);

            switch ($def['status']) {
                case 'draft':
                    $factory = $factory->draft($def['frequency']);
                    break;
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
                    'currency' => 'EUR',
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
