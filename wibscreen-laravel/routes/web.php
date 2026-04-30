<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\WorkspaceController;
use App\Models\Plan;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/* ── Home ───────────────────────────────────────────── */
Route::get('/', fn() => view('pages.home'))->name('home');

use App\Http\Controllers\SubscriptionController;
// use App\Models\Plan;

/* ── Pricing ────────────────────────────────────────── */
Route::get('/pricing', function () {
    $plans = Plan::all();
    return view('pages.pricing', compact('plans'));
})->name('pricing');

/* ── Auth ───────────────────────────────────────────── */
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/upgrade', [SubscriptionController::class, 'upgrade'])->name('upgrade')->middleware('auth');

/* ── Email Verification ────────────────────────────── */
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/* ── Dashboard (Workspace) ──────────────────────── */
Route::get('/dashboard', function () {
    $collections = auth()->user()->collections()->with(['tabs', 'notes'])->get();
    return view('pages.dashboard', compact('collections'));
})->name('dashboard')->middleware(['auth', 'verified']);
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
});

/* ── Legal Pages ────────────────────────────────────── */
Route::get('/privacy', fn() => view('pages.privacy'))->name('privacy');
Route::get('/terms', fn() => view('pages.terms'))->name('terms');
Route::get('/security', fn() => view('pages.security'))->name('security');
Route::get('/cookies', fn() => view('pages.cookies'))->name('cookies');
