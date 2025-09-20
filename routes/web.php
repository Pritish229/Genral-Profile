<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendors\VendorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Employees\EmployeeController;
use App\Http\Controllers\Students\StudentBankController;
use App\Http\Controllers\Students\StudentBasicController;
use App\Http\Controllers\Students\StudentMediaController;
use App\Http\Controllers\Employees\EmployeeBankController;
use App\Http\Controllers\Employees\EmployeeMediaController;
use App\Http\Controllers\Students\StudentAddressController;
use App\Http\Controllers\Students\StudentContactController;
use App\Http\Controllers\Employees\EmployeeAddressController;
use App\Http\Controllers\Employees\EmployeeContactController;
use App\Http\Controllers\Employees\EmployeePrimaryController;
use App\Http\Controllers\Students\StudentDocumentsController;
use App\Http\Controllers\Employees\EmployeeDocumentsController;
use App\Http\Controllers\Vendors\VendorBusinessProfileController;

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
    // Employee Basic info
    Route::get('{id}/Basicinfo', [EmployeePrimaryController::class, 'index'])->name('employees.Basicinfo');
    Route::get('List/All', [EmployeePrimaryController::class, 'listAll'])->name('employees.Employeeslist.paginate');
    Route::get('List/Employees', [EmployeePrimaryController::class, 'employeelist'])->name('employees.Employeelist.all');
    Route::get('List/ManagerList', [EmployeePrimaryController::class, 'managerList'])->name('employees.managerList');
    Route::post('{emp_id}/Basicinfo/Update', [EmployeePrimaryController::class, 'updateDetails'])->name('employees.UpdateDetails');
    Route::get('{emp_id}/Basicinfo/Details', [EmployeePrimaryController::class, 'basicDetails'])->name('employees.BasicDetails');
     Route::get('{emp_id}/Basicinfo/Manage', [EmployeePrimaryController::class, 'manageDetail'])->name('employees.manageDetail');
    Route::get('{emp_id}/view/Details', [EmployeePrimaryController::class, 'viewDetails'])->name('employees.viewDetails');

    // Employee Address
    Route::get('{id}/Address', [EmployeeAddressController::class, 'index'])->name('employees.Address');
    Route::get('{id}/Manage/Addresses', [EmployeeAddressController::class, 'manageAddress'])->name('employees.addresses.manage');
    Route::post('{employee_id}/Manage/Addresses', [EmployeeAddressController::class, 'storeAddress'])->name('employees.addresses.store');
    Route::put('{employee}/addresses/{address}', [EmployeeAddressController::class, 'updateAddress'])->name('employees.addresses.update');
    Route::delete('{employee}/addresses/{address}', [EmployeeAddressController::class, 'deleteAddress'])->name('employees.addresses.delete');
    Route::get('{employee_id}/Address/Permanent', [EmployeeAddressController::class, 'permanentAddress'])->name('employees.Address.Permanent');
    Route::get('{id}/Get/Addresses', [EmployeeAddressController::class, 'getAddresses'])->name('employees.addresses.list');

    // Employee Contact
    Route::get('{id}/Contact', [EmployeeContactController::class, 'index'])->name('employees.Contact');
    Route::post('{employee_id}/Address/storeContact', [EmployeeContactController::class, 'storeContact'])->name('employees.address.StoreContact');
    Route::get('{id}/Contact/Permanent', [EmployeeContactController::class, 'permanentContact'])->name('employees.Contact.Permanent');
    Route::get('{id}/Manage/Contacts', [EmployeeContactController::class, 'manageContact'])->name('employees.contacts.manageContact');
    Route::get('{id}/Get/Contacts', [EmployeeContactController::class, 'getContacts'])->name('employees.contacts.list');
    Route::post('{employee_id}/Manage/Contacts', [EmployeeContactController::class, 'storeContact'])
        ->name('employees.contacts.store');
    Route::put('{employee}/contacts/{contact}', [EmployeeContactController::class, 'updateContact'])
        ->name('employees.contacts.update');
    Route::delete('{employee}/contacts/{contact}', [EmployeeContactController::class, 'deleteContact'])
        ->name('employees.contacts.delete');

    // Employee Bank
    Route::get('{id}/Bank', [EmployeeBankController::class, 'index'])->name('employees.Bank');
    Route::post('{id}/saveBank', [EmployeeBankController::class, 'saveBank'])->name('employees.Bank.saveBank');
    Route::get('{id}/bank-list', [EmployeeBankController::class, 'employeeBankList'])->name('employees.bank.list');
    Route::get('{id}/manageBank', [EmployeeBankController::class, 'manageBankForm'])->name('employees.bank.manage');
    Route::get('{id}/manageBank/{account_id}', [EmployeeBankController::class, 'editBank'])->name('employees.bank.manage.edit');
    Route::delete('{id}/deleteBank/{account_id}', [EmployeeBankController::class, 'deleteBank'])->name('employees.bank.delete');

    // Employee Document
    Route::get('{id}/Document', [EmployeeDocumentsController::class, 'index'])->name('employees.Document');
    Route::post('{id}/storeDocument', [EmployeeDocumentsController::class, 'storeDocument'])->name('employees.Bank.StoreDocument');
    Route::get('{id}/manageDocument', [EmployeeDocumentsController::class, 'managedocument'])->name('employees.document.manage');
    Route::get('{id}/documents', [EmployeeDocumentsController::class, 'getDocuments']);
    Route::get('{employee_id}/documents/{doc_id}', [EmployeeDocumentsController::class, 'getDocument']);
    Route::delete('{employee_id}/documents/{doc_id}', [EmployeeDocumentsController::class, 'deleteDocument']);
    Route::put('{employee_id}/documents/{doc_id}', [EmployeeDocumentsController::class, 'updateDocument']);

    // Employee Media
    Route::get('{id}/Media', [EmployeeMediaController::class, 'index'])->name('employees.Media');
    Route::get('{id}/Media/manage', [EmployeeMediaController::class, 'manageindex'])->name('employees.manageindex');
    Route::post('{id}/storeMedia', [EmployeeMediaController::class, 'storeMedia'])->name('employees.Bank.storeMedia');
    Route::get('{employee}/{media}/medias/details', [EmployeeMediaController::class, 'show'])->name('employees.medias.show');
    Route::put('{employee}/{media}/update', [EmployeeMediaController::class, 'updateMedia'])->name('employees.medias.update');
    Route::delete('{employee_id}/{media}/delete', [EmployeeMediaController::class, 'destroy'])->name('employees.medias.destroy');
    Route::get('{id}/medias/list', [EmployeeMediaController::class, 'getMedias'])->name('employees.medias.list');
});

Route::prefix('vendors/')->group(function () {
    
    Route::get('create', [VendorController::class, 'create'])->name('vendors.create');
    Route::post('store', [VendorController::class, 'store'])->name('vendors.store');


    Route::get('{id}/BusinessInfo', [VendorBusinessProfileController::class, 'index'])->name('vendor.BusinessInfo');
});