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

class AuthController extends Controller
{
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
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plan' => $request->plan ?? 'free',
        ]);

        // Create Default Collection
        Collection::create([
            'user_id' => $user->id,
            'name' => 'Work',
            'icon' => 'fas fa-briefcase',
        ]);

        event(new Registered($user));

        Auth::login($user);
        Cookie::queue('wb_user_authenticated', 'true', 43200); // 30 days

        // Send Welcome Email
        Mail::send('emails.welcome', ['user' => $user], function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Welcome to Wibscreen!');
        });

        return redirect()->route('dashboard')->with('signup_success', "Welcome! Your ".ucfirst($user->plan)." plan has been activated.");
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

    public function upgrade(Request $request)
    {
        $plan = $request->query('plan');
        $paymentId = $request->query('payment_id');

        if (!in_array($plan, ['free', 'pro', 'business'])) {
            return back();
        }

        $user = Auth::user();
        $user->plan = $plan;
        $user->save();

        $duration = $request->query('duration', '1 Month');
        $msg = "Plan upgraded to ".ucfirst($plan)." successfully for $duration!";
        
        if ($paymentId) {
            $msg .= " (Payment ID: $paymentId)";
        }

        // Send Payment Success Email
        Mail::send('emails.payment-success', [
            'user' => $user,
            'plan' => $plan,
            'duration' => $duration,
            'paymentId' => $paymentId
        ], function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Payment Successful — Wibscreen Pro');
        });

        return redirect()->route('dashboard')->with('signup_success', $msg);
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
