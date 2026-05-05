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
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send welcome email to {$user->email}: " . $e->getMessage());
        }

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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget('wb_user_authenticated'));
        return redirect('/');
    }
}
