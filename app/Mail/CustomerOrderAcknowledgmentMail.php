<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerOrderAcknowledgmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $estimate;
    public $estimateItems;

    /**
     * Create a new message instance.
     *
     * @param $estimate
     * @param $estimateItems
     */
    public function __construct($estimate, $estimateItems)
    {
        $this->estimate = $estimate;
        $this->estimateItems = $estimateItems;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.customer_acknowledgment')
                    ->subject('Your Order Acknowledgment')
                    ->with([
                        'estimate' => $this->estimate,
                        'estimateItems' => $this->estimateItems,
                    ]);
    }
}
