<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Students\StudentController;


Route::get('/', [DashboardController::class, 'dashBoardPage'])->name('Admin.Dashboard');

// Students Module 

Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');