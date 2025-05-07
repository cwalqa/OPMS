<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\QuickbooksEstimates;

class AdminOrderDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $reason;

    public function __construct(QuickbooksEstimates $order, string $reason)
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('A Client Order has been Declined')
                    ->view('emails.order_declined_admin')
                    ->with([
                        'order' => $this->order,
                        'clientName' => $this->order->customer_name,
                        'reason' => $this->reason,
                    ]);
    }
}