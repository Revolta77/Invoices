<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceSentMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $paymentDetails
     */
    public function __construct(
        public string $messageBody,
        public array $paymentDetails,
        public string $pdfFilename,
        public string $pdfBinary,
        public ?string $extraAttachmentPath = null,
        public ?string $extraAttachmentName = null,
    ) {}

    public function envelope(): Envelope
    {
        $fromEmail = $this->paymentDetails['from_email'] ?? config('mail.from.address');
        $fromName = $this->paymentDetails['from_name'] ?? config('mail.from.name');

        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [new Address($fromEmail, $fromName)],
            subject: $this->paymentDetails['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.invoice-sent',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [
            Attachment::fromData(fn () => $this->pdfBinary, $this->pdfFilename)
                ->withMime('application/pdf'),
        ];

        if ($this->extraAttachmentPath && is_file($this->extraAttachmentPath)) {
            $attachments[] = Attachment::fromPath($this->extraAttachmentPath)
                ->as($this->extraAttachmentName ?? basename($this->extraAttachmentPath));
        }

        return $attachments;
    }
}
