<?php

namespace App\Services\Invoices;

use App\Jobs\GeneratePDF;
use App\Jobs\SendCancellationInvoiceMail;
use App\Jobs\SendInvoiceMail;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\UniqueNumber;
use App\Modules\InvoiceTemplates\Models\TemplateManager;
use App\Services\Shared\LineItemProcessorService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Response;

class InvoiceService
{
    public function __construct(private readonly TemplateManager $templateManager)
    {
    }

    public function store(array $attributes): Invoice
    {
        return Invoice::create($attributes);
    }

    public function destroy(Invoice $invoice): void
    {
        $invoice->delete();
    }

    public function open(Invoice $invoice, array $attributes): void
    {
        $data = [
            'date_due' => now()->addDays((int) $attributes['days_till_due']),
            'performed_when' => $attributes['performed_when'],
            'invoice_no' => UniqueNumber::generateNumber(
                model: $invoice,
                format: config('unique-numbers.'.Invoice::class.'.format', 2)
            ),
            'status' => Invoice::STATUS_OPEN,
        ];

        $invoice->update($data);

        GeneratePDF::dispatch($invoice);
    }

    public function paid(Invoice $invoice, string $paidAt): void
    {
        $invoice->update([
            'status' => Invoice::STATUS_PAID,
            'paid_at' => $paidAt,
        ]);
    }

    public function pdf(Invoice $invoice): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $tenantKey = $invoice->customer->tenant->getTenantKey();

        if ($invoice->status !== Invoice::STATUS_CANCELLATION_INVOICE) {
            $template = $this->templateManager->getTemplate(templateKey: 'invoice.pdf', tenantKey: $tenantKey);
        } else {
            $template = $this->templateManager->getTemplate(templateKey: 'invoice-cancellation.pdf', tenantKey: $tenantKey);
        }

        $pdfDocument = $template->render(['invoice' => $invoice]);

        $response = \response($pdfDocument, 200);
        $response->header('Content-Type', 'application/pdf');

        return $response;


        //if (count($invoice->documents) > 0) {
        //    $document = Storage::disk($invoice->documents[0]->storage)->get($invoice->documents[0]->path);
        //
        //    $response = Response::make($document, 200);
        //
        //    $response->header('Content-Type', $invoice->documents[0]->mime_type);
        //
        //    return $response;
        //}
    }

    public function sendMail(Invoice $invoice): void
    {
        if ($invoice->status !== Invoice::STATUS_CANCELLATION_INVOICE) {
            SendInvoiceMail::dispatch($invoice);
        } else {
            SendCancellationInvoiceMail::dispatch($invoice);
        }
    }

    public function openAndSendMail(Invoice $invoice, array $attributes): void
    {
        $data = [
            'date_due' => now()->addDays((int) $attributes['days_till_due']),
            'performed_when' => $attributes['performed_when'],
            'invoice_no' => UniqueNumber::generateNumber(
                model: $invoice,
                format: config('unique-numbers.'.Invoice::class.'.format', 2)
            ),
            'status' => Invoice::STATUS_OPEN,
        ];

        $invoice->update($data);

        Bus::chain([
            new GeneratePDF($invoice),
            new SendInvoiceMail($invoice),
        ])->dispatch();
    }

    public function addLineItem(array $attributes): LineItem
    {
        $attributes = LineItemProcessorService::processLineItemData($attributes);

        return LineItem::create($attributes);
    }

    public static function updateOpenToOverdue()
    {
        return Invoice::where('status', '=', Invoice::STATUS_OPEN)
            ->whereDate('date_due', '<=', today())
            ->update(['status' => Invoice::STATUS_OVERDUE]);
    }

    public static function totalsUpdate(Invoice $invoice): void
    {
        $invoice->unsetRelation('lineItems');

        $invoice->update([
            'total_without_tax' => $invoice->lineItems->sum('without_tax'),
            'total_with_tax' => $invoice->lineItems->sum('with_tax'),
        ]);
    }

    public static function setCurrencyIfNull(Invoice $invoice, LineItem $lineItem): void
    {
        $invoice->currency ? null : $invoice->update(['currency' => $lineItem->currency]);
    }

    /**
     * @return array{percentage: mixed, base: mixed, value: (float | int)}[]
     */
    public static function totalPerTax(Collection $lineItems): array
    {
        $perTax = [];
        foreach ($lineItems->unique('tax_rate')->pluck('tax_rate') as $tax_rate) {
            $group = $lineItems->where('tax_rate', '=', $tax_rate);
            $perTax[] = [
                'percentage' => $tax_rate,
                'base' => $group->sum('without_tax'),
                'value' => $group->sum('with_tax') - $group->sum('without_tax'),
            ];
        }

        return $perTax;
    }

    public function cancel(Invoice $originalInvoice): void
    {
        $invoiceReplica = $originalInvoice->replicate();

        $originalInvoice->update([
            'status' => Invoice::STATUS_CANCELLED,
        ]);

        $cancellationInvoice = $invoiceReplica->fill([
            'status' => Invoice::STATUS_CANCELLATION_INVOICE,
            'mail_status' => Invoice::MAIL_STATUS_NOT_MAILABLE,
            'invoice_no' => UniqueNumber::generateNumber(
                model: $originalInvoice,
                format: config('unique-numbers.'.Invoice::class.'.format', 2)
            ),
            'cancelled_invoice_id' => $originalInvoice->id,
        ]);

        $cancellationInvoice->save();

        GeneratePDF::dispatch($cancellationInvoice);
    }
}
