<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     * @param array{
     *     "merchantName": string,
     *     "ingredientName": string,
     *     "threshold": int,
     *     "current": int
     * } $item
     *
     * @param string $merchantName
     *
     */
    public function __construct(private readonly array $item, private readonly string $merchantName)
    {

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Low Stock Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.lowStock',
            with: ["name" => $this->merchantName, "item" => $this->item]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
