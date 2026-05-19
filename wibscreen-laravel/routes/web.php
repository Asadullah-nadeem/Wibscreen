<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\WorkspaceController;
use App\Models\Plan;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/* ── Home ───────────────────────────────────────────── */
Route::get('/', fn() => view('pages.home'))->name('home');

Route::get('/load-balancer-test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Request successfully load balanced!',
        'handled_by' => env('SERVICE_NAME', 'unknown'),
        'timestamp' => now()->toIso8601String(),
    ]);
});

use App\Http\Controllers\SubscriptionController;
// use App\Models\Plan;

/* ── Pricing ────────────────────────────────────────── */
Route::get('/pricing', function () {
    $plans = Plan::all();
    return view('pages.pricing', compact('plans'));
})->name('pricing');

/* ── Auth ───────────────────────────────────────────── */
Route::get('/sign-in', [AuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/sign-in', [AuthController::class, 'login'])->middleware('guest');
Route::get('/sinup', [AuthController::class, 'showSignup'])->middleware('guest')->name('signup');
Route::post('/sinup', [AuthController::class, 'signup'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Legacy Redirects to prevent 404s
Route::get('/login', fn() => redirect()->route('login'));
Route::get('/signup', fn() => redirect()->route('signup'));
Route::get('/register', fn() => redirect()->route('signup'))->name('register');
Route::get('/forgot-password', fn() => redirect()->route('password.request'));

/* ── Password Reset ────────────────────────────────── */
Route::get('/froget-password', [AuthController::class, 'showForgotPassword'])->middleware('guest')->name('password.request');
Route::post('/froget-password', [AuthController::class, 'sendResetLink'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.update');

Route::get('/upgrade', [SubscriptionController::class, 'upgrade'])->name('upgrade')->middleware('auth');

/* ── Email Verification ────────────────────────────── */
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    $user = auth()->user();

    // If premium plan is pending, redirect to pricing with auto-pay trigger
    if ($user && in_array($user->plan, ['pro', 'business']) && $user->plan_status === 'pending') {
        return redirect()->route('pricing', ['checkout' => 1]);
    }

    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/* ── Dashboard (Workspace) ──────────────────────── */
Route::get('/dashboard', [WorkspaceController::class, 'index'])->name('dashboard')->middleware(['auth', 'verified']);
Route::get('/dasboard', fn() => redirect()->route('dashboard')); // Redirect typo to real route

/* ── Main Pages ─────────────────────────────────────── */
Route::get('/about', fn() => view('pages.about'))->name('about');
Route::get('/pricing', function () {
    $plans = Plan::all();
    return view('pages.pricing', compact('plans'));
})->name('pricing');
Route::get('/support', fn() => view('pages.support'))->name('support')->middleware(['auth', 'verified']);
Route::post('/support', [SupportController::class, 'send'])->middleware(['auth', 'verified']);

/* ── Workspace API ─────────────────────────────── */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/collections', [WorkspaceController::class, 'createCollection'])->name('collections.create');
    Route::post('/collections/{id}/rename', [WorkspaceController::class, 'renameCollection'])->name('collections.rename');
    Route::delete('/collections/{id}', [WorkspaceController::class, 'deleteCollection'])->name('collections.delete');

    Route::post('/tabs', [WorkspaceController::class, 'createTab'])->name('tabs.create');
    Route::delete('/tabs/{id}', [WorkspaceController::class, 'deleteTab'])->name('tabs.delete');

    // Notes
    Route::post('/notes', [\App\Http\Controllers\NoteController::class, 'store']);
    Route::put('/notes/{id}', [\App\Http\Controllers\NoteController::class, 'update']);
    Route::delete('/notes/{id}', [\App\Http\Controllers\NoteController::class, 'destroy']);

    Route::post('/track-usage', [WorkspaceController::class, 'trackUsage'])->name('usage.track');

    // CSRF Refresh
    // Route::get('/refresh-csrf', function () {
    //     return response()->json(['token' => csrf_token()]);
    // });

    // Account Management
    Route::post('/account/deactivate', [AuthController::class, 'deactivate'])->name('account.deactivate');
    Route::delete('/account/delete', [AuthController::class, 'deleteAccount'])->name('account.delete');

    // Terminal Security Tokens
    Route::get('/terminal/token', function () {
        $tokenStr = \Illuminate\Support\Str::random(16);
        \App\Models\TerminalToken::create([
            'user_id' => auth()->id(),
            'token' => $tokenStr,
            'expires_at' => now()->addMinutes(2)
        ]);
        return response()->json(['token' => $tokenStr]);
    });
});

Route::get('/terminal/auth', function (Illuminate\Http\Request $request) {
    $tokenVal = $request->query('token');
    if (!$tokenVal) {
        return response('Unauthorized: Missing Token', 401);
    }

    $token = \App\Models\TerminalToken::where('token', $tokenVal)
        ->where('expires_at', '>', now())
        ->first();

    if (!$token) {
        return response('Unauthorized: Invalid or Expired Token', 401);
    }

    return response('OK', 200);
});

/* ── Legal Pages ────────────────────────────────────── */
Route::get('/privacy', fn() => view('pages.privacy'))->name('privacy');
Route::get('/terms', fn() => view('pages.terms'))->name('terms');
Route::get('/security', fn() => view('pages.security'))->name('security');
Route::get('/cookies', fn() => view('pages.cookies'))->name('cookies');
