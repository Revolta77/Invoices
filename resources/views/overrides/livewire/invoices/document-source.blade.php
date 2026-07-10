<div id="invoice-document-source" class="ek-invoice-document-source" aria-hidden="true">
    @include('livewire.invoices.document-body', [
        'preview' => $this->invoicePreviewData,
        'interactiveQr' => false,
    ])
</div>
