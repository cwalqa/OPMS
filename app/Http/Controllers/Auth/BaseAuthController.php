<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Services\PHPMailerService;
use Illuminate\Support\Str;

abstract class BaseAuthController extends Controller
{
    protected $mailer;
    protected $guard = 'web'; // Default guard
    protected $maxLoginAttempts = 5;
    protected $decayMinutes = 15;
    protected $twoFactorExpireMinutes = 10;

    public function __construct(PHPMailerService $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Rate limit login attempts
     */
    protected function checkRateLimit(Request $request, $email)
    {
        $ip = $request->ip();
        $rateLimiterKey = "login_attempts:{$email}:{$ip}:{$this->guard}";

        if (RateLimiter::tooManyAttempts($rateLimiterKey, $this->maxLoginAttempts)) {
            throw ValidationException::withMessages([
                'email' => [
                    'Too many login attempts. Try again in ' . 
                    RateLimiter::availableIn($rateLimiterKey) . ' seconds.'
                ],
            ]);
        }

        return $rateLimiterKey;
    }

    /**
     * Generate and send 2FA code
     */
    protected function generate2FACode($user, $emailSubject)
    {
        $twoFactorCode = strtoupper(Str::random(8)); // Increased to 8 characters
        $user->two_factor_code = Hash::make($twoFactorCode);
        $user->two_factor_expires_at = now()->addMinutes($this->twoFactorExpireMinutes);
        $user->save();

        // Generate email body
        $body = $this->generate2FAEmailBody($twoFactorCode);
        
        try {
            // Send directly using PHPMailer instead of queueing
            $mailerService = app(PHPMailerService::class);
            $result = $mailerService->send(
                $user->email,
                $emailSubject,
                $body
            );
            
            Log::info("2FA code sent directly to {$user->email}", ['result' => $result]);
        } catch (\Exception $e) {
            Log::error("Failed to send 2FA code: " . $e->getMessage());
            // Optional: Queue as fallback
            \App\Jobs\Send2FACodeMailJob::dispatch($user->email, $emailSubject, $body);
        }

        return $twoFactorCode;
    }

    /**
     * Generate HTML email body for 2FA code
     */
    protected function generate2FAEmailBody($code)
{
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>2FA Code</title>
    </head>
    <body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
        <div style="max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
            <h2 style="color: #333;">Two-Factor Authentication Code</h2>
            <p style="font-size: 16px;">Your verification code is:</p>
            <div style="font-size: 28px; font-weight: bold; letter-spacing: 3px; background-color: #eef2f7; padding: 12px; text-align: center; border-radius: 6px;">
                ' . $code . '
            </div>
            <p style="font-size: 14px; margin-top: 20px;">This code will expire in ' . $this->twoFactorExpireMinutes . ' minutes.</p>
            <p style="font-size: 12px; color: #777;">If you did not request this, please secure your account immediately.</p>
        </div>
    </body>
    </html>';
}


    /**
     * Verify 2FA code
     */
    protected function verify2FACode($user, $inputCode)
    {
        if (now()->isAfter($user->two_factor_expires_at)) {
            return false; // Code has expired
        }

        return Hash::check($inputCode, $user->two_factor_code);
    }

    /**
     * Check for rate limiting on 2FA attempts
     */
    protected function check2FARateLimit($userId)
    {
        $maxAttempts = 5;
        $decayMinutes = 10;
        $key = "2fa_attempts:{$this->guard}:{$userId}";

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            throw ValidationException::withMessages([
                'two_factor_code' => ['Too many 2FA attempts. Please try again after ' . 
                                    RateLimiter::availableIn($key) . ' seconds.'],
            ]);
        }

        return $key;
    }

    /**
     * Handle session regeneration and logout
     */
    protected function performLogout(Request $request)
    {
        Auth::guard($this->guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}