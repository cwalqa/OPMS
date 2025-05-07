<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\AdminPurchaseOrderNotificationMail;
use Illuminate\Support\Facades\Mail;

class SendAdminOrderMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipients;
    protected $estimate;
    protected $orderItems;

    public function __construct(array $recipients, $estimate, $orderItems)
    {
        $this->recipients = $recipients;
        $this->estimate = $estimate;
        $this->orderItems = $orderItems;
    }

    public function handle()
    {
        Mail::to($this->recipients)
            ->send(new AdminPurchaseOrderNotificationMail($this->estimate, $this->orderItems));
    }
}
