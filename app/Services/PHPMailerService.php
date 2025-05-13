<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class PHPMailerService
{
    public function send($to, $subject, $body)
    {
        try {
            $mail = new PHPMailer(true);

            // SMTP Settings
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption', 'tls');
            $mail->Port = config('mail.mailers.smtp.port', 587);

            // Critical anti-spoofing fixes:
            $mail->Helo = 'gmail.com'; // Correct EHLO domain for Gmail SMTP
            $mail->MessageID = '<' . time() . '.' . uniqid() . '@gmail.com>'; // Message-ID domain aligned to Gmail

            // Sender & Headers
            $fromAddress = config('mail.from.address', 'developer.datapluzz@gmail.com');
            $fromName = config('mail.from.name', 'ColorWrap Inc');
            $mail->setFrom($fromAddress, $fromName);
            $mail->addReplyTo($fromAddress, $fromName);

            // Recipient
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            // Plain text alternative
            $mail->AltBody = $this->createPlainTextVersion($body);

            // Charset
            $mail->CharSet = 'UTF-8';

            // Optional Debugging (disable in production)
            // $mail->SMTPDebug = 2;
            // $mail->Debugoutput = function($str, $level) {
            //     Log::debug("SMTP DEBUG [$level]: $str");
            // };

            $mail->send();
            Log::info("PHPMailer success: Mail sent to {$to}");
            return true;
        } catch (Exception $e) {
            Log::error('PHPMailer failed: ' . $mail->ErrorInfo);
            return false;
        }
    }

    protected function createPlainTextVersion($html)
    {
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/p>/i', "\n\n", $text);
        $text = preg_replace('/<li>/i', "- ", $text);
        $text = preg_replace('/<\/li>/i', "\n", $text);

        $text = strip_tags($text);
        $text = html_entity_decode($text);
        $text = wordwrap($text, 70, "\r\n");

        return $text;
    }
}
