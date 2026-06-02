<?php

namespace Tests\Feature\Jobs;

use App\Jobs\GeneratePDF;
use App\Jobs\SendInvoiceMail;
use App\Jobs\SubscriptionHandler;
use App\Models\Customer;
use App\Models\MasterInvoice;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubscriptionHandlerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::factory(['owner_id' => $this->user])->create();
        $this->customer = Customer::factory()->create();
    }

    public function testThreeOverdueMasterInvoicesAreOpenedAndSendViaEmail()
    {
        Queue::fake();

        $count = 3;

        MasterInvoice::factory()
            ->active()
            ->overdue()
            ->count($count)
            ->create();

        SubscriptionHandler::dispatch();

        Queue::assertPushedWithChain(GeneratePDF::class, [
            SendInvoiceMail::class,
        ]);
    }

    public function testPerformedWhenDay()
    {
        $this->travelTo(Carbon::create(2021, 11, 10));

        $masterInvoice = MasterInvoice::factory()
            ->active()
            ->create(['next_print' => now(), 'billing_frequency' => MasterInvoice::BILLING_DAY]);

        self::assertEquals('Mittwoch, November 2021', $masterInvoice->buildPerformedWhen());
    }

    public function testPerformedWhenWeek()
    {
        $this->travelTo(Carbon::create(2021, 11, 10));

        $masterInvoice = MasterInvoice::factory()->create([
            'next_print' => now(),
            'billing_frequency' => MasterInvoice::BILLING_WEEK,
        ]);

        self::assertEquals('KW 45 2021', $masterInvoice->buildPerformedWhen());
    }

    public function testPerformedWhenMonth()
    {
        $this->travelTo(Carbon::create(2021, 11, 10));

        $masterInvoice = MasterInvoice::factory()
            ->active()
            ->create(['next_print' => now(), 'billing_frequency' => MasterInvoice::BILLING_MONTH]);

        self::assertEquals('November 2021', $masterInvoice->buildPerformedWhen());
    }

    public function testPerformedWhenQuarterYear()
    {
        $this->travelTo(Carbon::create(2021, 11, 10));

        $masterInvoice = MasterInvoice::factory()
            ->active()
            ->create(['next_print' => now(), 'billing_frequency' => MasterInvoice::BILLING_QUARTER_YEAR]);

        self::assertEquals('August 2021 - November 2021', $masterInvoice->buildPerformedWhen());
    }

    public function testPerformedWhenHalfYear()
    {
        $this->travelTo(Carbon::create(2021, 11, 10));

        $masterInvoice = MasterInvoice::factory()
            ->active()
            ->create(['next_print' => now(), 'billing_frequency' => MasterInvoice::BILLING_HALF_YEAR]);

        self::assertEquals('Mai 2021 - November 2021', $masterInvoice->buildPerformedWhen());
    }
}
