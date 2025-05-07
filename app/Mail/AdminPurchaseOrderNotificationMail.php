<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPurchaseOrderNotificationMail extends Mailable
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
        return $this->view('emails.admin_notification')
                    ->subject('New Purchase Order Notification')
                    ->with([
                        'estimate' => $this->estimate,
                        'estimateItems' => $this->estimateItems,
                    ]);
    }
}
