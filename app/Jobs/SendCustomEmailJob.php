<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\PHPMailerService;

class SendCustomEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $subject;
    protected $body;
    
    // Add this property to make job synchronous
    public $connection = 'sync';

    public function __construct($to, $subject, $body)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle()
    {
        \Log::info("Running PHPMailer job to: {$this->to}");

        $result = app(PHPMailerService::class)->send(
            $this->to,
            $this->subject,
            $this->body
        );

        \Log::info("Mail sent: " . json_encode($result));
    }
}