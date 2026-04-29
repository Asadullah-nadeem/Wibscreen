<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Wibscreen — Web Routes
|--------------------------------------------------------------------------
| All public static routes for the Wibscreen platform.
| No database required — all views are pure Blade templates.
|--------------------------------------------------------------------------
*/

/* ── Home ───────────────────────────────────────────── */
Route::get('/', fn() => view('pages.home'))->name('home');

/* ── Auth ───────────────────────────────────────────── */
Route::get('/login',  fn() => view('pages.login'))->name('login');
Route::get('/signup', fn() => view('pages.signup'))->name('signup');

/* ── Dashboard (Workspace) ──────────────────────── */
Route::get('/dashboard',     fn() => view('pages.dashboard'))->name('dashboard');
Route::get('/dasboard',      fn() => view('pages.dashboard'));      // typo alias
Route::get('/dasboard', fn() => view('pages.dashboard'));      // legacy .html alias

/* ── Main Pages ─────────────────────────────────────── */
Route::get('/about',   fn() => view('pages.about'))->name('about');
Route::get('/pricing', fn() => view('pages.pricing'))->name('pricing');
Route::get('/support', fn() => view('pages.support'))->name('support');

/* ── Legal Pages ────────────────────────────────────── */
Route::get('/privacy',  fn() => view('pages.privacy'))->name('privacy');
Route::get('/terms',    fn() => view('pages.terms'))->name('terms');
Route::get('/security', fn() => view('pages.security'))->name('security');
Route::get('/cookies',  fn() => view('pages.cookies'))->name('cookies');
