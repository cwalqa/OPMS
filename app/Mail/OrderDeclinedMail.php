<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\QuickbooksEstimates;

class OrderDeclinedMail extends Mailable
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
        return $this->subject('Your Order Has Been Declined')
                    ->view('emails.order_declined')
                    ->with([
                        'order' => $this->order,
                        'clientName' => $this->order->customer_name ?? 'Valued Customer',
                        'reason' => $this->reason,
                    ]);
    }
}
// This class is responsible for sending an email notification to the customer when their order is declined.