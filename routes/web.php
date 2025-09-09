<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Students\StudentBankController;
use App\Http\Controllers\Students\StudentBasicController;
use App\Http\Controllers\Students\StudentMediaController;
use App\Http\Controllers\Students\StudentAddressController;
use App\Http\Controllers\Students\StudentContactController;
use App\Http\Controllers\Students\StudentDocumentsController;


Route::get('/', [DashboardController::class, 'dashBoardPage'])->name('Admin.Dashboard');

// Students Module 

Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');

// Student Basic info 
Route::get('/students/{id}/Basicinfo', [StudentBasicController::class, 'index'])->name('students.Basicinfo');
Route::get('/students/Basicinfo/List', [StudentBasicController::class, 'studentlist'])->name('students.Studentlist');
Route::get('/students/List/All', [StudentBasicController::class, 'listAll'])->name('students.Studentlist.all');
Route::get('/students/Details/{student_id}/page', [StudentBasicController::class, 'studentDetails'])->name('students.Studentlist.studentDetailsPage');

Route::get('/students/{student_id}/Basicinfo/Details', [StudentBasicController::class, 'basicDetails'])->name('students.BasicDetails');
Route::post('/students/{student_id}/Basicinfo/Update', [StudentBasicController::class, 'updateDetails'])->name('students.UpdateDetails');

// Student Address
Route::get('/students/{id}/Address', [StudentAddressController::class, 'index'])->name('students.Address');
Route::get('/students/{student_id}/Address/Permanent', [StudentAddressController::class, 'permanentAddress'])->name('students.Address.Permanent');
Route::post('/students/{student_id}/Address/Update', [StudentAddressController::class, 'update'])->name('students.address.Update');

// 
Route::get('/students/{id}/Contact', [StudentContactController::class, 'index'])->name('students.Contact');
Route::post('/students/{student_id}/Address/storeContact', [StudentContactController::class, 'storeContact'])->name('students.address.StoreContact');
Route::get('/students/{id}/Contact/Permanent', [StudentContactController::class, 'permanentContact'])->name('students.Contact.Permanent');

// Student Bank
Route::get('/students/{id}/Bank', [StudentBankController::class, 'index'])->name('students.Bank');
Route::post('/students/{id}/storeBank', [StudentBankController::class, 'storeBank'])->name('students.Bank.StoreBank');

// Student Document
Route::get('/students/{id}/Document', [StudentDocumentsController::class, 'index'])->name('students.Document');
Route::post('/students/{id}/storeDocument', [StudentDocumentsController::class, 'storeDocument'])->name('students.Bank.StoreDocument');


// Student Media
Route::get('/students/{id}/Media', [StudentMediaController::class, 'index'])->name('students.Media');
Route::post('/students/{id}/storeMedia', [StudentMediaController::class, 'storeMedia'])->name('students.Bank.storeMedia');


