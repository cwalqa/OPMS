<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\CustomerOrderAcknowledgmentMail;
use Illuminate\Support\Facades\Mail;

class SendCustomerOrderMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $estimate;
    protected $orderItems;

    public function __construct($estimate, $orderItems)
    {
        $this->estimate = $estimate;
        $this->orderItems = $orderItems;
    }

    public function handle()
    {
        Mail::to($this->estimate->bill_email)
            ->send(new CustomerOrderAcknowledgmentMail($this->estimate, $this->orderItems));
    }
}

