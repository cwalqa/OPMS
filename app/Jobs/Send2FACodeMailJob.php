<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class Send2FACodeMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $subject;
    protected $body;
    
    // Add this property to make job synchronous
    public $connection = 'sync';

    public function __construct(string $email, string $subject, string $body)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle()
    {
        \Log::info("Running Mail job to: {$this->email}");

        try {
            Mail::html($this->body, function ($message) {
                $message->to($this->email)
                        ->subject($this->subject);
            });

            \Log::info("Mail sent to {$this->email}");
        } catch (\Throwable $e) {
            \Log::error("Laravel Mail failed to {$this->email}: " . $e->getMessage());
            throw new \Exception("Laravel Mail failed to send 2FA code.");
        }
    }
}