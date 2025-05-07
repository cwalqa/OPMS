<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\QuickbooksCustomer;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends BaseAuthController
{
    protected $guard = 'web';
    
    public function showLoginForm()
    {
        // Run the command to refresh the QuickBooks access token
        try {
            Artisan::call('quickbooks:generate-access-token');
            Log::info('QuickBooks access token refreshed successfully.');
        } catch (\Exception $e) {
            Log::error('Error refreshing QuickBooks access token: ' . $e->getMessage());
            return back()->withErrors(['error' => 'System error. Please try again later.']);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8', // Increased minimum password length
        ]);

        $email = $credentials['email'];
        
        // Check rate limiting
        $rateLimiterKey = $this->checkRateLimit($request, $email);

        // Generic message to avoid revealing account existence
        $invalidCredentialsMessage = 'Invalid login credentials. Please try again.';

        if (!Auth::attempt(['email' => $email, 'password' => $credentials['password']], $request->remember)) {
            RateLimiter::hit($rateLimiterKey, $this->decayMinutes * 60);
            
            Log::warning("Failed login attempt", [
                'email' => $email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->withErrors([
                'email' => $invalidCredentialsMessage,
            ])->withInput($request->except('password'));
        }

        RateLimiter::clear($rateLimiterKey);

        $customer = Auth::user();

        // Regenerate session to prevent session fixation
        Session::regenerate();

         // Add this line to explicitly mark as NOT verified
        Session::put('two_factor_verified', false);

        // Store minimal user data in session
        $request->session()->put('customer', [
            'id' => $customer->id,
            'customer_id' => $customer->customer_id,
            'display_name' => $customer->display_name,
            'email' => $customer->email,
        ]);

        // Generate and send 2FA code
        $this->generate2FACode($customer, "ColorWrap Verification Code");

        return redirect()->route('customer.2fa')
            ->with('success', 'Please check your email for verification code.');
    }

    public function show2faForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')
                ->with('error', 'Your session expired. Please log in again.');
        }
        
        return view('auth.2fa');
    }

    public function verify2fa(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')
                ->with('error', 'Your session expired. Please log in again.');
        }

        $request->validate([
            'two_factor_code' => 'required|alpha_num|size:8',
        ]);

        $customer = Auth::user();
        
        // Check rate limiting for 2FA attempts
        $rateLimitKey = $this->check2FARateLimit($customer->id);

        if ($this->verify2FACode($customer, $request->two_factor_code)) {
            $customer->resetTwoFactorCode();
            RateLimiter::clear($rateLimitKey);

            // Only set to true after successful verification
            Session::put('two_factor_verified', true);

            // Set session timeout - 2 hours
            config(['session.lifetime' => 120]);

            return redirect()->route('client.dashboard')
                ->with('success', 'Verification successful. Welcome back!');
        }

        // Log out and invalidate session on failed verification
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
        Session::flush();

        RateLimiter::hit($rateLimitKey, 10 * 60);

        return redirect()->route('login.form')
            ->withErrors(['two_factor_code' => 'The verification code is incorrect or expired.'])
            ->with('error', '2FA verification failed. You have been logged out for security reasons.');
    }


    public function resend2fa(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')
                ->with('error', 'Your session expired. Please log in again.');
        }

        $customer = Auth::user();
        
        // Rate limit resend attempts
        $key = "resend_2fa:{$customer->id}";
        if (RateLimiter::tooManyAttempts($key, 3)) { // Max 3 resends
            return back()->withErrors([
                'error' => 'Too many code resend attempts. Please try again after ' . 
                          RateLimiter::availableIn($key) . ' seconds.'
            ]);
        }
        
        RateLimiter::hit($key, 60); // 1 minute cooldown

        $this->generate2FACode($customer, "ColorWrap New Verification Code");

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    public function logout(Request $request)
    {
        $this->performLogout($request);
        
        // Clean up additional session data
        Session::flush();

        return redirect()->route('login.form')
            ->with('logout_message', 'You have been logged out successfully.');
    }
}