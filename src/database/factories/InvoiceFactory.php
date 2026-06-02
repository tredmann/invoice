<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Money;
use App\Models\UniqueNumber;
use App\Models\User;
use App\Services\Invoices\InvoiceService;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Invoice $invoice) {
            InvoiceService::totalsUpdate($invoice);
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
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'invoice_no' => null,
            'performed_when' => null,
            'days_till_due' => null,
            'date_due' => null,
            'status' => Invoice::STATUS_DRAFT,
            'mail_status' => Invoice::MAIL_STATUS_NOT_MAILABLE,
            'currency' => $this->faker->randomElement(Money::CURRENCIES),
            'total_with_tax' => null,
            'total_without_tax' => null,
        ];
    }

    public function open()
    {
        return $this->state(function () {
            return [
                'invoice_no' => UniqueNumber::generateNumber(
                    model:$this,
                    format: config('unique-numbers.'.Invoice::class.'.format', 2)
                ),
                'performed_when' => $this->faker->monthName.' '.$this->faker->year,
                'days_till_due' => ($days_till_due = $this->faker->randomElement(Invoice::DAYS_TILL_DUE)),
                'date_due' => now()->addDays($days_till_due),
                'status' => Invoice::STATUS_OPEN,
            ];
        });
    }

    public function paid()
    {
        return $this->state(function () {
            return [
                'status' => Invoice::STATUS_PAID,
                'paid_at' => now()->subDays($this->faker->numberBetween(1, 14)),
            ];
        });
    }

    public function overdue()
    {
        return $this->state(function () {
            return [
                'status' => Invoice::STATUS_OVERDUE,
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function () {
            return [
                'status' => Invoice::STATUS_CANCELLED,
            ];
        });
    }

    public function cancellationInvoice(Invoice $cancelledInvoice)
    {
        return $this->state(function () use ($cancelledInvoice) {
            return [
                'invoice_no' => UniqueNumber::generateNumber(
                    model: $this,
                    format: config('unique-numbers.'.Invoice::class.'.format', 2)
                ),
                'status' => Invoice::STATUS_CANCELLATION_INVOICE,
                'cancelled_invoice_id' => $cancelledInvoice->id,
                'customer_id' => $cancelledInvoice->customer_id,
                'currency' => $cancelledInvoice->currency,
            ];
        });
    }

    public function pdfError()
    {
        return $this->state(function () {
            return [
                'status' => Invoice::STATUS_OPEN_PDF_ERROR,
            ];
        });
    }

    public function withMailStatus(string $mailStatus)
    {
        return $this->state(function () use ($mailStatus) {
            return [
                'mail_status' => $mailStatus,
            ];
        });
    }
}
