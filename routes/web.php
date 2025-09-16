<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Employees\EmployeeController;
use App\Http\Controllers\Students\StudentBankController;
use App\Http\Controllers\Students\StudentBasicController;
use App\Http\Controllers\Students\StudentMediaController;
use App\Http\Controllers\Students\StudentAddressController;
use App\Http\Controllers\Students\StudentContactController;
use App\Http\Controllers\Employees\EmployeeAddressController;
use App\Http\Controllers\Employees\EmployeePrimaryController;
use App\Http\Controllers\Students\StudentDocumentsController;

Route::get('/', [DashboardController::class, 'dashBoardPage'])->name('Admin.Dashboard');

// Students Module 

Route::prefix('students/')->group(function () {

    Route::get('create', [StudentController::class, 'create'])->name('students.create');
    Route::post('store', [StudentController::class, 'store'])->name('students.store');

    // Student Basic info 
    Route::get('{id}/Basicinfo', [StudentBasicController::class, 'index'])->name('students.Basicinfo');
    Route::get('Basicinfo/List', [StudentBasicController::class, 'studentlist'])->name('students.Studentlist');
    Route::get('List/All', [StudentBasicController::class, 'listAll'])->name('students.Studentlist.all');
    Route::get('Details/{student_id}/page', [StudentBasicController::class, 'studentDetails'])->name('students.Studentlist.studentDetailsPage');

    Route::get('{student_id}/Basicinfo/Manage', [StudentBasicController::class, 'manageDetail'])->name('students.manageDetail');
    Route::get('{student_id}/Basicinfo/Details', [StudentBasicController::class, 'basicDetails'])->name('students.BasicDetails');
    Route::post('{student_id}/Basicinfo/Update', [StudentBasicController::class, 'updateDetails'])->name('students.UpdateDetails');
    
    // Student Address
    Route::get('{id}/Address', [StudentAddressController::class, 'index'])->name('students.Address');
    Route::get('{id}/Manage/Addresses', [StudentAddressController::class, 'manageAddress'])->name('students.addresses.manage');
    Route::get('{id}/Get/Addresses', [StudentAddressController::class, 'getAddresses'])->name('students.addresses.list');
    
    Route::post('{student_id}/Manage/Addresses', [StudentAddressController::class, 'storeAddress'])->name('students.addresses.store');
    Route::put('{student}/addresses/{address}', [StudentAddressController::class, 'updateAddress'])->name('students.addresses.update');
    Route::delete('{student}/addresses/{address}', [StudentAddressController::class, 'deleteAddress'])->name('students.addresses.delete');
    
    Route::get('{student_id}/Address/Permanent', [StudentAddressController::class, 'permanentAddress'])->name('students.Address.Permanent');
    
    
    Route::get('{id}/Contact', [StudentContactController::class, 'index'])->name('students.Contact');
    Route::post('{student_id}/Address/storeContact', [StudentContactController::class, 'storeContact'])->name('students.address.StoreContact');
    Route::get('{id}/Contact/Permanent', [StudentContactController::class, 'permanentContact'])->name('students.Contact.Permanent');
    
    Route::get('{id}/Manage/Contacts', [StudentContactController::class, 'manageContact'])->name('students.contacts.manageContact');
    Route::get('{id}/Get/Contacts', [StudentContactController::class, 'getContacts'])->name('students.contacts.list');
    Route::post('{student_id}/Manage/Contacts', [StudentContactController::class, 'storeContact'])
    ->name('students.contacts.store');
    Route::put('{student}/contacts/{contact}', [StudentContactController::class, 'updateContact'])
    ->name('students.contacts.update');
    Route::delete('{student}/contacts/{contact}', [StudentContactController::class, 'deleteContact'])
    ->name('students.contacts.delete');
    
    
    // Student Bank
    Route::get('{id}/Bank', [StudentBankController::class, 'index'])->name('students.Bank');
    Route::post('{id}/saveBank', [StudentBankController::class, 'saveBank'])->name('students.Bank.saveBank');
    Route::get('{id}/bank-list', [StudentBankController::class, 'studentBankList'])->name('students.bank.list');
    Route::get('{id}/manageBank', [StudentBankController::class, 'manageBankForm'])->name('students.bank.manage');
    Route::get('{id}/manageBank/{account_id}', [StudentBankController::class, 'editBank'])->name('students.bank.manage.edit');
    Route::delete('{id}/deleteBank/{account_id}', [StudentBankController::class, 'deleteBank'])->name('students.bank.delete');
    
    
    // Student Document
    Route::get('{id}/Document', [StudentDocumentsController::class, 'index'])->name('students.Document');
    Route::post('{id}/storeDocument', [StudentDocumentsController::class, 'storeDocument'])->name('students.Bank.StoreDocument');
    Route::get('{id}/manageDocument', [StudentDocumentsController::class, 'managedocument'])->name('students.document.manage');
    
    Route::get('{id}/documents', [StudentDocumentsController::class, 'getDocuments']);
    Route::get('{student_id}/documents/{doc_id}', [StudentDocumentsController::class, 'getDocument']);
    Route::delete('{student_id}/documents/{doc_id}', [StudentDocumentsController::class, 'deleteDocument']);
    Route::put('{student_id}/documents/{doc_id}', [StudentDocumentsController::class, 'updateDocument']);
    
    
    
    // Student Media
    Route::get('{id}/Media', [StudentMediaController::class, 'index'])->name('students.Media');
    Route::get('{id}/Media/manage', [StudentMediaController::class, 'manageindex'])->name('students.manageindex');
    Route::post('{id}/storeMedia', [StudentMediaController::class, 'storeMedia'])->name('students.Bank.storeMedia');
    Route::get('{student}/{media}/medias/details', [StudentMediaController::class, 'show'])->name('students.medias.show');
    Route::put('{student}/{media}/update', [StudentMediaController::class, 'updateMedia'])->name('students.medias.update');
    Route::delete('{student_id}/{media}/delete', [StudentMediaController::class, 'destroy'])->name('students.medias.destroy');
    Route::get('{id}/medias/list', [StudentMediaController::class, 'getMedias'])->name('students.medias.list');
});

Route::prefix('employees/')->group(function () {
    Route::get('create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('store', [EmployeeController::class, 'store'])->name('employees.store');
    
    Route::get('{id}/Basicinfo', [EmployeePrimaryController::class, 'index'])->name('employees.Basicinfo');
    Route::get('List/All', [EmployeePrimaryController::class, 'listAll'])->name('employees.Employeeslist.paginate');
    Route::get('List/Employees', [EmployeePrimaryController::class, 'employeelist'])->name('employees.Employeelist.all');
    Route::post('{emp_id}/Basicinfo/Update', [EmployeePrimaryController::class, 'updateDetails'])->name('employees.UpdateDetails');
    Route::get('{emp_id}/Basicinfo/Details', [EmployeePrimaryController::class, 'basicDetails'])->name('employees.BasicDetails');


    Route::get('{id}/Address', [EmployeeAddressController::class, 'index'])->name('students.Address');
    
    
});
