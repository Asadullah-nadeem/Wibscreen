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

        // --- NEW SECURITY: LOGOUT UNTIL VERIFIED ---
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget('wb_user_authenticated'));
        
        return redirect()->route('verification.notice')->with('status', 'Please verify your email address before accessing your ' . ucfirst($user->plan) . ' workspace.');
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
}
