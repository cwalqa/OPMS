<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\QuickbooksEstimates;

class OrderApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(QuickbooksEstimates $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Your Order has been Approved')
                    ->view('emails.order_approved')
                    ->with([
                        'order' => $this->order,
                        'clientName' => $this->order->customer_name, 
                    ]);
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}