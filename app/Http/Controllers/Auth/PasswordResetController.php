<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\QuickbooksCustomer;
use App\Services\PHPMailerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    /**
     * The mail service instance.
     *
     * @var PHPMailerService
     */
    protected $mailer;

    /**
     * Create a new controller instance.
     *
     * @param  PHPMailerService  $mailer
     * @return void
     */
    public function __construct(PHPMailerService $mailer)
    {
        // Removed the middleware call from here as it was causing the error
        $this->mailer = $mailer;
    }

    /**
     * Display the password reset request form.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        // Redirect to dashboard if already authenticated
        if (Auth::guard('web')->check()) {
            return redirect()->route('client.dashboard');
        }
        
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Redirect to dashboard if already authenticated
        if (Auth::guard('web')->check()) {
            return redirect()->route('client.dashboard');
        }
        
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        // Find the customer associated with this email
        $customer = QuickbooksCustomer::where('email', $request->email)->first();

        // Always return the same response regardless of whether we found the email
        // to prevent email enumeration attacks
        if (!$customer) {
            return back()->with([
                'status' => 'If your email exists in our system, you will receive a password reset link shortly.'
            ]);
        }

        // Delete any existing tokens for this email
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Generate a secure random token
        $token = Str::random(64);

        // Store the token in the database with expiration time
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        // Generate the reset URL with a signed route for security
        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            Carbon::now()->addMinutes(60),
            ['token' => $token, 'email' => $request->email]
        );

        try {
            // Render the reset email view to HTML
            $emailContent = view('emails.password_reset', [
                'resetUrl' => $resetUrl,
                'customer' => $customer
            ])->render();

            // Send the reset link email using PHPMailer
            $this->mailer->send(
                $request->email,
                'Reset Your Password',
                $emailContent
            );

            return back()->with([
                'status' => 'If your email exists in our system, you will receive a password reset link shortly.'
            ]);
        } catch (\Exception $e) {
            // Log the error but don't expose it to users
            logger()->error('Password reset email failed: ' . $e->getMessage());
            
            return back()->with([
                'status' => 'If your email exists in our system, you will receive a password reset link shortly.'
            ])->withErrors(['email_error' => 'There was an error sending the email. Please try again later.']);
        }
    }

    /**
     * Display the password reset form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        // Redirect to dashboard if already authenticated
        if (Auth::guard('web')->check()) {
            return redirect()->route('client.dashboard');
        }
        
        if (!$request->hasValidSignature()) {
            abort(401, 'This password reset link is invalid or has expired.');
        }

        return view('auth.passwords.reset')->with([
            'token' => $token, 
            'email' => $request->email
        ]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'max:255'],
            'password' => [
                'required', 
                'confirmed', 
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        // Find the password reset record
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        // Check if token exists and is valid
        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Invalid or expired password reset token.']);
        }

        // Check if token is expired (older than 60 minutes)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset token has expired.']);
        }

        // Verify token hash
        if (!Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Invalid password reset token.']);
        }

        // Find the customer and update the password
        $customer = QuickbooksCustomer::where('email', $request->email)->first();
        if (!$customer) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }

        // Update password using secure hashing
        $customer->password = Hash::make($request->password);
        $customer->save();

        // Delete all reset records for this email
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Automatically log the user in
        Auth::guard('web')->login($customer);

        // Send password changed notification email
        try {
            $emailContent = view('emails.password_changed', [
                'customer' => $customer
            ])->render();

            $this->mailer->send(
                $request->email,
                'Your Password Has Been Changed',
                $emailContent
            );
        } catch (\Exception $e) {
            logger()->error('Password change notification email failed: ' . $e->getMessage());
        }

        return redirect()->route('client.dashboard')
            ->with('status', 'Your password has been reset successfully.');
    }
}