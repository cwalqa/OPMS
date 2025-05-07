<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\PHPMailerService;
use Illuminate\Support\Facades\Mail;

class Send2FACodeMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $subject;
    protected $body;

    public function __construct(string $email, string $subject, string $body)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle()
{
    \Log::info("Running queued Mail job to: {$this->email}");

    try {
        Mail::html($this->body, function ($message) {
            $message->to($this->email)
                    ->subject($this->subject);
        });

        \Log::info("Mail sent from queue to {$this->email}");
    } catch (\Throwable $e) {
        \Log::error("Laravel Mail failed in queued job to {$this->email}: " . $e->getMessage());
        throw new \Exception("Laravel Mail failed to send 2FA code.");
    }
}
}
