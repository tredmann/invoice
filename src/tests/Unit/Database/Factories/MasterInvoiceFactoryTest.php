<?php

namespace Tests\Unit\Database\Factories;

use App\Models\Customer;
use App\Models\MasterInvoice;
use App\Models\Tenant\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterInvoiceFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        Tenant::factory(['owner_id' => $user])->create();
        Customer::factory()->create();
    }

    public function test_draft_state_sets_status_and_configurable_frequency(): void
    {
        $master = MasterInvoice::factory()->draft('1 week')->create();

        $this->assertSame(MasterInvoice::STATUS_DRAFT, $master->status);
        $this->assertSame('1 week', $master->billing_frequency);
        $this->assertNull($master->next_print, 'Drafts have no next_print');
    }
}
