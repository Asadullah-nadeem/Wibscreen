<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlanUpgradedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plan;
    public $expiryDate;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $planSlug, $expiryDate)
    {
        $this->user = $user;
        $this->plan = \App\Models\Plan::where('slug', $planSlug)->first();
        $this->expiryDate = $expiryDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank You! Your ' . ($this->plan->name ?? 'Pro') . ' Plan is Now Active',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.plan-upgraded',
            with: [
                'userName' => $this->user->name,
                'planName' => $this->plan->name,
                'expiry' => $this->expiryDate ? $this->expiryDate->format('d M, Y') : 'Lifetime',
                'features' => $this->plan->features ?? [],
            ],
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
