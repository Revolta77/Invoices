<?php

namespace App\Services;

use App\Models\Invoice;
use App\Support\InvoicePreviewBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfDocument;

class InvoicePdfService
{
    public function make(Invoice $invoice): DomPdfDocument
    {
        $preview = InvoicePreviewBuilder::fromInvoice($invoice, forPdf: true);

        return Pdf::loadView('invoices.pdf', compact('preview'))
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);
    }

    public function output(Invoice $invoice): string
    {
        return $this->make($invoice)->output();
    }

    public function filename(Invoice $invoice): string
    {
        return InvoicePreviewBuilder::filename($invoice);
    }
}
