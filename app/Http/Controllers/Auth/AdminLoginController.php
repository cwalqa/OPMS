<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\QuickbooksAdmin;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminLoginController extends BaseAuthController
{
    protected $guard = 'admin';
    
    public function showLoginForm()
    {
        return view('auth.admin_login');
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

        $admin = QuickbooksAdmin::where('email', $email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            RateLimiter::hit($rateLimiterKey, $this->decayMinutes * 60);
            
            Log::warning("Failed admin login attempt", [
                'email' => $email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->withErrors([
                'email' => $invalidCredentialsMessage,
            ])->withInput($request->except('password'));
        }

        RateLimiter::clear($rateLimiterKey);

        Auth::guard('admin')->login($admin);

        // Regenerate session to prevent session fixation
        Session::regenerate();

         // Add this line to explicitly mark as NOT verified
        Session::put('two_factor_verified', false);

        // Store minimal user data in session
        $request->session()->put('admin', [
            'admin_id' => $admin->id,
            'display_name' => $admin->name,
            'email' => $admin->email,
        ]);
        
        // Generate and send 2FA code
        $this->generate2FACode($admin, "CWI Admin Verification Code");

        return redirect()->route('admin.2fa')
            ->with('success', 'Please check your email for verification code.');
    }

    public function show2faForm()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login.form')
                ->with('error', 'Your session expired. Please log in again.');
        }
        
        return view('auth.admin_2fa');
    }

    public function verify2fa(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login.form')
                ->with('error', 'Your session expired. Please log in again.');
        }

        $request->validate([
            'two_factor_code' => 'required|alpha_num|size:8',
        ]);

        $admin = Auth::guard('admin')->user();
        
        // Check rate limiting for 2FA attempts
        $rateLimitKey = $this->check2FARateLimit($admin->id);

        if ($this->verify2FACode($admin, $request->two_factor_code)) {
            $admin->resetTwoFactorCode();
            RateLimiter::clear($rateLimitKey);
            
             // Only set to true after successful verification
             Session::put('two_factor_verified', true);
             
            // Set shorter session timeout for admin - 1 hour
            config(['session.lifetime' => 60]);
            
            return redirect()->route('admin.dashboard')
                ->with('success', 'Verification successful. Welcome back!');
        }

         // Log out and invalidate session on failed verification
         Auth::logout();
         Session::invalidate();
         Session::regenerateToken();
         Session::flush();
 
         RateLimiter::hit($rateLimitKey, 10 * 60);
 
         return redirect()->route('admin.login.form')
             ->withErrors(['two_factor_code' => 'The verification code is incorrect or expired.'])
             ->with('error', 'Verification failed. You have been logged out for security reasons.');
    }

    public function resend2fa(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login.form')
                ->with('error', 'Your session expired. Please log in again.');
        }

        $admin = Auth::guard('admin')->user();
        
        // Rate limit resend attempts
        $key = "resend_2fa:admin:{$admin->id}";
        if (RateLimiter::tooManyAttempts($key, 3)) { // Max 3 resends
            return back()->withErrors([
                'error' => 'Too many code resend attempts. Please try again after ' . 
                          RateLimiter::availableIn($key) . ' seconds.'
            ]);
        }
        
        RateLimiter::hit($key, 60); // 1 minute cooldown

        $this->generate2FACode($admin, "CWI Admin New Verification Code");

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    public function logout(Request $request)
    {
        $this->performLogout($request);
        
        // Clean up additional session data
        Session::flush();

        return redirect()->route('admin.login.form')
            ->with('logout_message', 'You have been logged out successfully.');
    }
}