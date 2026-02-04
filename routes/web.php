<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\StudentsPage;
use App\Livewire\PostPage;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/students', StudentsPage::class)->name('students.index');
Route::get('/posts', PostPage::class);