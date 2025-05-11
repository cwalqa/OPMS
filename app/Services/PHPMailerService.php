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

            // SMTP settings
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption', 'tls');
            $mail->Port = config('mail.mailers.smtp.port', 587);
            
            // Add debug settings - temporarily enable for troubleshooting
            $mail->SMTPDebug = 2; // Set to 0 in production
            $mail->Debugoutput = function($str, $level) {
                Log::debug("SMTP DEBUG [$level]: $str");
            };

            // Sender
            $fromAddress = config('mail.from.address') ?? 'datapluzzdeveloper@gmail.com';
            $fromName = config('mail.from.name') ?? 'ColorWrap Inc';
            $mail->setFrom($fromAddress, $fromName);
            
            // Add Reply-To header (some providers require this)
            $mail->addReplyTo($fromAddress, $fromName);

            // Recipient
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            // Ensure proper plain text alternative - create a better plain text version
            $plainText = $this->createPlainTextVersion($body);
            $mail->AltBody = $plainText;

            // Try with standard encoding instead of base64
            $mail->CharSet = 'UTF-8';
            // $mail->Encoding = 'base64'; // Comment out to use default 8bit encoding

            // Add message ID for better tracking
            $mail->MessageID = '<' . time() . '.' . uniqid() . '@' . parse_url(config('app.url'), PHP_URL_HOST) . '>';

            $result = $mail->send();
            Log::info("PHPMailer success: Mail sent to {$to}");
            return true;
        } catch (Exception $e) {
            Log::error('PHPMailer failed: ' . $mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Create a better plain text version of HTML content
     */
    protected function createPlainTextVersion($html)
    {
        // First convert <br>, <p>, etc to newlines
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/p>/i', "\n\n", $text);
        $text = preg_replace('/<li>/i', "- ", $text);
        $text = preg_replace('/<\/li>/i', "\n", $text);
        
        // Then strip all remaining HTML tags
        $text = strip_tags($text);
        
        // Decode HTML entities
        $text = html_entity_decode($text);
        
        // Wrap text at 70 chars
        $text = wordwrap($text, 70, "\r\n");
        
        return $text;
    }
}








// Optional: Debug output for troubleshooting
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = function ($str, $level) {
        //     \Log::debug("SMTP DEBUG [$level]: $str");
        // };
