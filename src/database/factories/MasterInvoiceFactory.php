<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\MasterInvoice;
use App\Models\Money;
use App\Models\User;
use App\Services\MasterInvoices\MasterInvoiceService;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterInvoiceFactory extends Factory
{
    protected $model = MasterInvoice::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (MasterInvoice $invoice) {
            MasterInvoiceService::totalsUpdate($invoice);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'days_till_due' => null,
            'billing_frequency' => null,
            'next_print' => null,
            'status' => MasterInvoice::STATUS_DRAFT,
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
            'total_with_tax' => null,
            'total_without_tax' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function active()
    {
        return $this->state(function () {
            return [
                'status' => MasterInvoice::STATUS_ACTIVE,
                'days_till_due' => $this->faker->randomElement(MasterInvoice::DAYS_TILL_DUE),
                'billing_frequency' => $this->faker->randomElement(MasterInvoice::BILLING_FREQUENCIES),
                'next_print' => now()
                    ->addDays($this->faker->randomDigitNotNull)
                    ->toDateString(),
            ];
        });
    }

    public function paused()
    {
        return $this->state(function () {
            return [
                'status' => MasterInvoice::STATUS_PAUSED,
            ];
        });
    }

    public function overdue()
    {
        return $this->state(function () {
            return [
                'next_print' => now()
                    ->subDays($this->faker->randomDigit)
                    ->toDateString(),
            ];
        });
    }

    public function draft(string $billingFrequency)
    {
        return $this->state(function () use ($billingFrequency) {
            return [
                'status' => MasterInvoice::STATUS_DRAFT,
                'billing_frequency' => $billingFrequency,
                'days_till_due' => 14,
                'next_print' => null,
            ];
        });
    }
}
