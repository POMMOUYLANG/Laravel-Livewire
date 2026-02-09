<?php

use App\Livewire\Auth\LoginPage;
use App\Livewire\Dashboard;
use App\Livewire\StudentsPage;
use App\Livewire\PostPage;
use App\Livewire\TeacherPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Redirect root based on auth
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/
Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/students', StudentsPage::class)->name('students.index');

    Route::get('/posts', PostPage::class)->name('posts.index');

    Route::get('/teachers', TeacherPage::class)->name('teachers.index');
});
