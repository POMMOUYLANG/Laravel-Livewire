<?php

use App\Livewire\StudentsPage;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/students', StudentsPage::class)->name('students.index');
