<?php


namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    public function send($to, $subject, $body)
{
    try {
        $mail = new PHPMailer(true);

        // SMTP settings
        $mail->isSMTP();
        $mail->Host = config('mail.mailers.smtp.host', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = config('mail.mailers.smtp.username');
        $mail->Password = config('mail.mailers.smtp.password');
        $mail->SMTPSecure = config('mail.mailers.smtp.encryption', 'tls');
        $mail->Port = config('mail.mailers.smtp.port', 587);

        // Sender
        $fromAddress = config('mail.from.address') ?? 'datapluzzdeveloper@gmail.com';
        $fromName = config('mail.from.name') ?? 'ColorWrap Inc';
        $mail->setFrom($fromAddress, $fromName);

        // Recipient
        $mail->addAddress($to);

        // Headers
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body); // ✳️ Plain text fallback required by Gmail

        // Encoding
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // Optional: Debug output for troubleshooting
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = function ($str, $level) {
        //     \Log::debug("SMTP DEBUG [$level]: $str");
        // };

        $mail->send();
        \Log::info("PHPMailer success: Mail sent to {$to}");
        return true;
    } catch (Exception $e) {
        \Log::error('PHPMailer failed in queued job: ' . $e->getMessage());
        return false;
    }
}

}
