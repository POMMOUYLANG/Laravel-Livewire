<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\StudentsPage;
use App\Livewire\PostPage;
use App\Livewire\TeacherPage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/students', StudentsPage::class)->name('students.index');
Route::get('/posts', PostPage::class)->name('posts.index');
Route::get('/', Dashboard::class);
Route::get('/teachers', TeacherPage::class)->name('teachers.index');