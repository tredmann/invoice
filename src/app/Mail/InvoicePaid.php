<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoicePaid extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var Invoice
     */
    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    //    /**
    //     * Build the message.
    //     *
    //     * @return $this
    //     */
    //    public function build()
    //    {
    //        return $this->from($user->email)
    //                    ->view('default.emails.invoice.paid');
    //    }
}
