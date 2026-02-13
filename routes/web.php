<?php

use App\Http\Controllers\SsoController;
use App\Http\Controllers\SmisAuthController;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Dashboard;
use App\Livewire\StudentsPage;
use App\Livewire\PostPage;
use App\Livewire\TeacherPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Home Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

/*
|------------------------------------------------------------------
| SSO Background / API Sync (External Server-to-Server)
|------------------------------------------------------------------
*/
// This is called by the SSO server, not the user browser.
Route::post('/sso/sync', [SsoController::class, 'sync'])->name('sso.sync');

/*
|------------------------------------------------------------------
| Guest Routes (Unauthenticated users only)
|------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');

    // Flow for SsoController (Browser Redirect)
    Route::get('/sso/login', [SsoController::class, 'redirect'])->name('sso.login');
    Route::get('/sso/callback', [SsoController::class, 'callback'])->name('sso.callback');

    // Flow for SmisAuthController (API/Frontend Token Exchange)
    Route::post('/auth/smis/store', [SmisAuthController::class, 'store'])->name('smis.auth.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Logged-in users only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Core Pages
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/students', StudentsPage::class)->name('students.index');
    Route::get('/posts', PostPage::class)->name('posts.index');
    Route::get('/teachers', TeacherPage::class)->name('teachers.index');

    // Logout Routes
    // Unified logout: Use SmisAuthController for primary logout as it handles gateway cleanup
    Route::post('/logout', [SmisAuthController::class, 'destroy'])->name('logout');

    // Specifically for Browser-flow logout
    Route::post('/sso/logout', [SsoController::class, 'logout'])->name('sso.logout');
});
