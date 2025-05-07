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

            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption', 'tls');
            $mail->Port = config('mail.mailers.smtp.port', 587);

            $fromAddress = config('mail.from.address') ?? 'datapluzzdeveloper@gmail.com';
            $fromName = config('mail.from.name') ?? 'ColorWrap Inc';

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            \Log::error('PHPMailer failed in queued job: ' . $e->getMessage());
            return false;
        }
    }
}
