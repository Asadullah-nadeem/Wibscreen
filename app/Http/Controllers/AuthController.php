<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function showForgotPassword()
    {
        return view('pages.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('pages.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(\Illuminate\Support\Str::random(60));

                $user->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function showSignup(Request $request)
    {
        $plan = $request->query('plan', 'free');
        return view('pages.signup', compact('plan'));
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'plan' => 'required|string|in:free,pro,business',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plan' => $request->plan ?? 'free',
            'plan_status' => (in_array($request->plan, ['pro', 'business']) ? 'pending' : 'active'),
        ]);

        // Create Default Collection (Workspace)
        Collection::create([
            'user_id' => $user->id,
            'name' => 'Work',
            'icon' => 'fas fa-briefcase',
        ]);

        event(new Registered($user));

        Auth::login($user);
        Cookie::queue('wb_user_authenticated', 'true', 43200); // 30 days

        // Send Welcome Email
        try {
            Mail::to($user->email)->send(new \App\Mail\WelcomePlanEmail($user));
            
            // Log Payment Lead if plan is premium
            if (in_array($user->plan, ['pro', 'business'])) {
                \App\Models\PaymentLead::create([
                    'user_id' => $user->id,
                    'plan_slug' => $user->plan,
                    'status' => 'pending'
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send welcome email to {$user->email}: " . $e->getMessage());
        }

        return redirect()->route('verification.notice')->with('verified_pop', true);
    }

    public function showLogin()
    {
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if user exists (including soft-deleted)
        $user = User::withTrashed()->where('email', $credentials['email'])->first();

        if ($user) {
            // Case 1: Account is soft-deleted (Scheduled for deletion)
            if ($user->trashed()) {
                return back()->withErrors([
                    'email' => 'Your account is scheduled for deletion. Please contact support if this is a mistake.',
                ])->onlyInput('email');
            }

            // Case 2: Account is deactivated (Suspended)
            if ($user->isDeactivated()) {
                if (Auth::attempt($credentials, $request->remember)) {
                    // Send reactivation verification mail
                    try {
                        Mail::to($user->email)->send(new \App\Mail\AccountReactivatedEmail($user->name));
                    } catch (\Exception $e) {}
                    
                    // Reactivate and continue
                    $user->update(['account_status' => 'active', 'deactivated_at' => null]);
                    
                    $request->session()->regenerate();
                    Cookie::queue('wb_user_authenticated', 'true', 43200);
                    return redirect()->intended('dashboard')->with('success', 'Your account has been reactivated! Welcome back.');
                }
            }
        }

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            Cookie::queue('wb_user_authenticated', 'true', 43200); // 30 days
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget('wb_user_authenticated'));
        return redirect('/');
    }

    /**
     * Deactivate account (Suspend)
     */
    public function deactivate(Request $request)
    {
        $user = Auth::user();
        $userName = $user->name;
        $userEmail = $user->email;

        $user->update([
            'account_status' => 'suspended',
            'deactivated_at' => \Carbon\Carbon::now()
        ]);
        
        // Send Deactivation Email
        try {
            Mail::to($userEmail)->send(new \App\Mail\AccountDeactivatedEmail($userName));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send deactivation email to {$userEmail}: " . $e->getMessage());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget('wb_user_authenticated'));

        return redirect('/')->with('info', 'Your account has been deactivated. You can sign in anytime to reactivate it.');
    }

    /**
     * Permanently delete account
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        $userName = $user->name;
        $userEmail = $user->email;
        
        // Send Deletion Email
        try {
            Mail::to($userEmail)->send(new \App\Mail\AccountDeletedEmail($userName));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send deletion email to {$userEmail}: " . $e->getMessage());
        }

        // Soft delete the user (marks as scheduled for deletion)
        $user->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget('wb_user_authenticated'));

        return redirect('/')->with('success', 'Your account is now scheduled for deletion and all access has been revoked.');
    }
}
