<?php

namespace App\Jobs;

use App\Mail\CancellationInvoiceMail;
use App\Models\Invoice;
use App\Services\SettingService\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendCancellationInvoiceMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private Invoice $invoice)
    {
    }

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        SettingService::overwriteSettings($this->invoice->customer->tenant);

        $hadFailure = false;

        foreach ($this->invoice->customer->customerMailReceivers as $customerMailReceiver) {
            try {
                Mail::to($customerMailReceiver)->send(new CancellationInvoiceMail(
                    $customerMailReceiver,
                    $this->invoice,
                    $this->invoice->getInvoiceDocument(),
                    $this->invoice->getAttachment(),
                ));
            } catch (Throwable $e) {
                $hadFailure = true;
                report($e);
            }
        }

        $this->invoice->update([
            'mail_status' => $hadFailure ? Invoice::MAIL_STATUS_ERROR : Invoice::MAIL_STATUS_MAILED,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $this->invoice->update([
            'mail_status' => Invoice::MAIL_STATUS_ERROR,
        ]);
    }
}
