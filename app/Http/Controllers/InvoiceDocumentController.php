<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoicePdfService;
use App\Support\ActiveCompanyProfile;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvoiceDocumentController extends Controller
{
    public function pdf(Request $request, Invoice $invoice, InvoicePdfService $pdfService): Response
    {
        $this->authorizeInvoice($invoice);

        $pdf = $pdfService->make($invoice);
        $filename = $pdfService->filename($invoice);

        if ($request->boolean('inline') || $request->boolean('print')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    protected function authorizeInvoice(Invoice $invoice): void
    {
        $profileId = ActiveCompanyProfile::id();

        abort_unless($profileId && (int) $invoice->company_profile_id === (int) $profileId, 403);
        abort_unless((int) $invoice->user_id === (int) auth()->id(), 403);
    }
}
