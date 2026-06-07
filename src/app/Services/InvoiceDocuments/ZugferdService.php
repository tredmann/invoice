<?php

declare(strict_types=1);

namespace App\Services\InvoiceDocuments;

use App\Exceptions\ZugferdGenerationException;
use App\Models\Invoice;
use App\Models\LineItem;
use App\Models\Tenant\GeneralInfo;
use App\Models\Tenant\LegalInfo;
use horstoeko\zugferd\codelists\ZugferdCountryCodes;
use horstoeko\zugferd\codelists\ZugferdCurrencyCodes;
use horstoeko\zugferd\codelists\ZugferdInvoiceType;
use horstoeko\zugferd\codelists\ZugferdVatCategoryCodes;
use horstoeko\zugferd\codelists\ZugferdVatTypeCodes;
use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferd\ZugferdDocumentPdfBuilder;
use horstoeko\zugferd\ZugferdProfiles;
use Throwable;

class ZugferdService
{
    /**
     * Embed a ZUGFeRD (EN 16931) compliant XML payload into the provided
     * PDF bytes and return the resulting PDF/A-3 bytes.
     *
     * @param  Invoice  $invoice  fully loaded invoice (with tenant -> generalInfo/legalInfo, customer, lineItems)
     * @param  string   $pdfBytes raw PDF bytes (e.g. produced by WeasyPrint)
     *
     * @throws ZugferdGenerationException when required Company Legal Info is missing or the library fails
     */
    public function embed(Invoice $invoice, string $pdfBytes): string
    {
        $legalInfo = $invoice->customer->tenant->currentLegalInfo;
        $generalInfo = $invoice->customer->tenant->currentGeneralInfo;

        $this->assertLegalInfoIsComplete($legalInfo);

        if ($generalInfo === null) {
            throw new ZugferdGenerationException(
                'Cannot generate ZUGFeRD invoice: Company Profile (general info) is missing.'
            );
        }

        $firstLineItem = $invoice->lineItems->first();
        $currency = $invoice->currency ?? ($firstLineItem !== null ? $firstLineItem->currency : null);
        if ($currency !== 'EUR') {
            throw new ZugferdGenerationException(
                "Invoice #{$invoice->invoice_no}: ZUGFeRD documents require EUR currency (got '{$currency}'). Non-EUR invoices cannot be issued as ZUGFeRD EN 16931 documents."
            );
        }

        try {
            $this->resolveCountryCode($generalInfo->country ?? '');
        } catch (ZugferdGenerationException) {
            throw new ZugferdGenerationException(
                "Company Profile has an unrecognised country '{$generalInfo->country}'. Set it to a supported value (e.g. 'DE' or 'Deutschland') before generating a ZUGFeRD document."
            );
        }

        try {
            $documentBuilder = $this->buildDocument($invoice, $legalInfo, $generalInfo);

            $pdfBuilder = ZugferdDocumentPdfBuilder::fromPdfString($documentBuilder, $pdfBytes);
            $pdfBuilder->generateDocument();

            return $pdfBuilder->downloadString();
        } catch (ZugferdGenerationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ZugferdGenerationException(
                'Failed to embed ZUGFeRD XML into PDF: '.$e->getMessage(),
                0,
                $e
            );
        }
    }

    private function assertLegalInfoIsComplete(?LegalInfo $legalInfo): void
    {
        if (!$legalInfo instanceof \App\Models\Tenant\LegalInfo) {
            throw new ZugferdGenerationException(
                'Cannot generate ZUGFeRD invoice: Company Legal Info is missing.'
            );
        }

        $required = [
            'vat_no' => $legalInfo->vat_no,
            'tax_no' => $legalInfo->tax_no,
            'iban' => $legalInfo->iban,
            'swift_bic' => $legalInfo->swift_bic,
        ];

        foreach ($required as $field => $value) {
            if (trim((string) $value) === '') {
                throw new ZugferdGenerationException(
                    sprintf('Cannot generate ZUGFeRD invoice: Company Legal Info field "%s" is required.', $field)
                );
            }
        }
    }

    private function buildDocument(Invoice $invoice, LegalInfo $legalInfo, GeneralInfo $generalInfo): ZugferdDocumentBuilder
    {
        $isCredit = $invoice->status === Invoice::STATUS_CANCELLATION_INVOICE;
        $typeCode = $isCredit ? ZugferdInvoiceType::CREDITNOTE : ZugferdInvoiceType::INVOICE;

        $currency = $this->resolveCurrencyCode($invoice->currency);

        $documentBuilder = ZugferdDocumentBuilder::createNew(ZugferdProfiles::PROFILE_EN16931);

        $documentBuilder->setDocumentInformation(
            $invoice->invoice_no ?? '',
            $typeCode,
            $invoice->getInvoiceDate(),
            $currency
        );

        $this->applySeller($documentBuilder, $generalInfo, $legalInfo);
        $this->applyBuyer($documentBuilder, $invoice);
        $this->applyPayment($documentBuilder, $invoice, $legalInfo);
        $this->applyLineItems($documentBuilder, $invoice);
        $this->applyTotals($documentBuilder, $invoice);

        return $documentBuilder;
    }

    private function applySeller(ZugferdDocumentBuilder $builder, GeneralInfo $generalInfo, LegalInfo $legalInfo): void
    {
        $builder->setDocumentSeller($generalInfo->name ?? '');
        $builder->setDocumentSellerAddress(
            $generalInfo->street ?? '',
            '',
            '',
            $generalInfo->postal ?? '',
            $generalInfo->city ?? '',
            $this->resolveCountryCode($generalInfo->country)
        );
        $builder->setDocumentSellerContact(
            $generalInfo->owner ?? '',
            '',
            '',
            $generalInfo->fax ?? '',
            $generalInfo->email ?? ''
        );
        // VAT registration (VA / ZugferdReferenceCodeQualifiers::VAT_REGI_NUMB)
        $builder->addDocumentSellerVATRegistrationNumber($legalInfo->vat_no);
        // Tax number (FC / ZugferdReferenceCodeQualifiers::FISC_NUMB)
        $builder->addDocumentSellerTaxNumber($legalInfo->tax_no);
    }

    private function applyBuyer(ZugferdDocumentBuilder $builder, Invoice $invoice): void
    {
        $customer = $invoice->customer;
        $buyerName = trim((string) ($customer->company ?: $customer->name));

        if ($buyerName === '') {
            throw new ZugferdGenerationException(
                "Invoice #{$invoice->invoice_no}: Customer has neither a company name nor a personal name. ZUGFeRD requires a buyer name (EN 16931 BT-44)."
            );
        }

        $builder->setDocumentBuyer($buyerName, $customer->customer_no);
        $builder->setDocumentBuyerAddress(
            $customer->street,
            '',
            '',
            $customer->postal,
            $customer->city,
            $this->resolveCountryCode($customer->country)
        );
    }

    private function applyPayment(ZugferdDocumentBuilder $builder, Invoice $invoice, LegalInfo $legalInfo): void
    {
        $builder->addDocumentPaymentMeanToCreditTransfer(
            $legalInfo->iban,
            $legalInfo->bank_institute ?? null,
            null,
            $legalInfo->swift_bic
        );

        $dueDate = $invoice->date_due->toDateTime();
        $description = sprintf('Zahlbar bis %s', $dueDate->format('d.m.Y'));

        $builder->addDocumentPaymentTerm($description, $dueDate);
    }

    private function applyLineItems(ZugferdDocumentBuilder $builder, Invoice $invoice): void
    {
        $position = 0;

        foreach ($invoice->lineItems as $lineItem) {
            $position++;
            /** @var LineItem $lineItem */
            $unitPrice = $this->centsToAmount((int) $lineItem->price_each);
            $quantity = (float) $lineItem->quantity;
            $lineTotal = $this->centsToAmount((int) $lineItem->without_tax);
            $taxPercent = $this->taxRateToPercent((float) $lineItem->tax_rate);
            $unitCode = $lineItem->unit->value;

            $builder->addNewPosition((string) $position);
            $builder->setDocumentPositionProductDetails(
                $lineItem->detail !== '' ? $lineItem->detail : 'Position '.$position,
                $lineItem->detail_plus !== '' ? $lineItem->detail_plus : null
            );
            $builder->setDocumentPositionGrossPrice($unitPrice);
            $builder->setDocumentPositionNetPrice($unitPrice);
            $builder->setDocumentPositionQuantity($quantity, $unitCode);
            $builder->addDocumentPositionTax(
                $this->resolveVatCategory($taxPercent),
                ZugferdVatTypeCodes::VALUE_ADDED_TAX,
                $taxPercent
            );
            $builder->setDocumentPositionLineSummation($lineTotal);
        }
    }

    private function applyTotals(ZugferdDocumentBuilder $builder, Invoice $invoice): void
    {
        $netByRate = [];
        $taxByRate = [];

        foreach ($invoice->lineItems as $lineItem) {
            $percent = $this->taxRateToPercent((float) $lineItem->tax_rate);
            $key = (string) $percent;

            $netByRate[$key] = ($netByRate[$key] ?? 0.0) + $this->centsToAmount((int) $lineItem->without_tax);
            $taxByRate[$key] = ($taxByRate[$key] ?? 0.0)
                + $this->centsToAmount((int) ($lineItem->with_tax - $lineItem->without_tax));
        }

        foreach ($netByRate as $key => $net) {
            $rate = (float) $key;
            $category = $this->resolveVatCategory($rate);
            $builder->addDocumentTax(
                $category,
                ZugferdVatTypeCodes::VALUE_ADDED_TAX,
                round($net, 2),
                round($taxByRate[$key], 2),
                $rate
            );
        }

        $lineTotal = $this->centsToAmount((int) $invoice->total_without_tax);
        $grandTotal = $this->centsToAmount((int) $invoice->total_with_tax);
        $taxTotal = round($grandTotal - $lineTotal, 2);

        $builder->setDocumentSummation(
            $grandTotal,
            $grandTotal,
            $lineTotal,
            0.0,
            0.0,
            $lineTotal,
            $taxTotal
        );
    }

    private function resolveVatCategory(float $taxPercent): string
    {
        if ($taxPercent <= 0.0) {
            return ZugferdVatCategoryCodes::ZERO_RATE_GOOD;
        }

        if ($taxPercent < 15.0) {
            return ZugferdVatCategoryCodes::LOWE_RATE;
        }

        return ZugferdVatCategoryCodes::STAN_RATE;
    }

    private function centsToAmount(int $cents): float
    {
        return round($cents / 100, 2);
    }

    private function taxRateToPercent(float $taxRate): float
    {
        return round($taxRate * 100, 2);
    }

    private function resolveCurrencyCode(?string $currency): string
    {
        if ($currency === null || $currency === '') {
            return ZugferdCurrencyCodes::EURO;
        }

        return strtoupper($currency);
    }

    private function resolveCountryCode(?string $country): string
    {
        return match (strtolower(trim((string) $country))) {
            'de', 'deutschland', 'germany' => ZugferdCountryCodes::GERMANY,
            default => throw new ZugferdGenerationException(
                "Cannot map country '{$country}' to an ISO 3166-1 alpha-2 code for ZUGFeRD generation."
            ),
        };
    }
}
