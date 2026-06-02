<?php

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\MasterInvoice;
use App\Models\Tenant\Tenant;
use App\Models\User;
use App\Services\MasterInvoices\MasterInvoiceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MasterInvoiceServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->customer = Customer::factory()->create();

        $this->masterInvoiceService = new MasterInvoiceService();
    }

    public function testGetOverdueNoneOverdue()
    {
        $overdues = $this->masterInvoiceService->getOverdues();

        self::assertSame($overdues->count(), 0);
    }

    public function testGetOverdueActiveButNotOverdue()
    {
        MasterInvoice::factory()
            ->active()
            ->for($this->customer)
            ->create();

        $overdues = $this->masterInvoiceService->getOverdues();

        self::assertSame($overdues->count(), 0);
    }

    public function testGetOverduesOneActiveOverdue()
    {
        MasterInvoice::factory()
            ->active()
            ->overdue()
            ->for($this->customer)
            ->create();

        $overdues = $this->masterInvoiceService->getOverdues();

        self::assertSame($overdues->count(), 1);
    }

    public function testGetOverduesThreeActiveOverdue()
    {
        MasterInvoice::factory()
            ->active()
            ->overdue()
            ->for($this->customer)
            ->count(3)
            ->create();

        $overdues = $this->masterInvoiceService->getOverdues();

        self::assertSame($overdues->count(), 3);
    }

    public function testGetNextPrintOneDay()
    {
        $overdue = MasterInvoice::factory()
            ->active()
            ->overdue()
            ->for($this->customer)
            ->create(['billing_frequency' => '1 day']);

        $nextPrint = $this->masterInvoiceService->getNextPrint($overdue);

        self::assertEquals($nextPrint, today()->addDay());
    }

    public function testGetNextPrintOneWeek()
    {
        $overdue = MasterInvoice::factory()
            ->active()
            ->overdue()
            ->for($this->customer)
            ->create(['billing_frequency' => '1 week']);

        $nextPrint = $this->masterInvoiceService->getNextPrint($overdue);

        self::assertEquals($nextPrint, today()->addWeek());
    }

    public function testGetNextPrintOneMonth()
    {
        $overdue = MasterInvoice::factory()
            ->active()
            ->overdue()
            ->for($this->customer)
            ->create(['billing_frequency' => '1 month']);

        $nextPrint = $this->masterInvoiceService->getNextPrint($overdue);

        self::assertEquals($nextPrint, today()->addMonth());
    }

    public function testGetNextPrintQuarterYear()
    {
        $overdue = MasterInvoice::factory()
            ->active()
            ->overdue()
            ->for($this->customer)
            ->create(['billing_frequency' => '3 months']);

        $nextPrint = $this->masterInvoiceService->getNextPrint($overdue);

        self::assertEquals($nextPrint, today()->addMonths(3));
    }

    public function testGetNextPrintHalfYear()
    {
        $overdue = MasterInvoice::factory()
            ->active()
            ->overdue()
            ->for($this->customer)
            ->create(['billing_frequency' => '6 months']);

        $nextPrint = $this->masterInvoiceService->getNextPrint($overdue);

        self::assertEquals($nextPrint, today()->addMonths(6));
    }

    public function testConvertToInvoiceWithMasterLineItems()
    {
        $overdueWithLineItems = MasterInvoice::factory()
            ->active()
            ->hasMasterLineItems(3)
            ->for($this->customer)
            ->create();

        $this->masterInvoiceService->convertToInvoice($overdueWithLineItems);

        self::assertSame(Invoice::all()->count(), 1);
        self::assertSame(LineItem::all()->count(), 3);
    }
}
