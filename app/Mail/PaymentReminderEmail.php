<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $planName;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $planSlug)
    {
        $this->user = $user;
        $plan = \App\Models\Plan::where('slug', $planSlug)->first();
        $this->planName = $plan->name ?? ucfirst($planSlug);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Complete Your Wibscreen ' . $this->planName . ' Purchase',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
