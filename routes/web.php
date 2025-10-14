<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendors\VendorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Customers\CustomerController;
use App\Http\Controllers\Employees\EmployeeController;
use App\Http\Controllers\Vendors\VendorBankController;
use App\Http\Controllers\Vendors\VendorMediaController;
use App\Http\Controllers\Students\StudentBankController;
use App\Http\Controllers\Students\StudentBasicController;
use App\Http\Controllers\Students\StudentMediaController;
use App\Http\Controllers\Vendors\VendorAddressController;
use App\Http\Controllers\Vendors\VendorContactController;
use App\Http\Controllers\Vendors\VendorProfileController;
use App\Http\Controllers\Customers\CustomerBankController;
use App\Http\Controllers\Employees\EmployeeBankController;
use App\Http\Controllers\Vendors\VendorDocumentController;
use App\Http\Controllers\Customers\CustomerMediaController;
use App\Http\Controllers\Employees\EmployeeMediaController;
use App\Http\Controllers\Students\StudentAddressController;
use App\Http\Controllers\Students\StudentContactController;
use App\Http\Controllers\Customers\CustomerAddressController;
use App\Http\Controllers\Customers\CustomerContactController;
use App\Http\Controllers\Employees\EmployeeAddressController;
use App\Http\Controllers\Employees\EmployeeContactController;
use App\Http\Controllers\Employees\EmployeePrimaryController;
use App\Http\Controllers\Students\StudentDocumentsController;
use App\Http\Controllers\Customers\CustomerDocumentController;
use App\Http\Controllers\Employees\EmployeeDocumentsController;
use App\Http\Controllers\Vendors\VendorOnlineProfileController;
use App\Http\Controllers\Vendors\VendorBusinessProfileController;
use App\Http\Controllers\Customers\CustomerBusinessProfileController;

Route::get('/', [DashboardController::class, 'dashBoardPage'])->name('Admin.Dashboard');
Route::get('/storage-link', function () {
    $targetFolder = storage_path('app/public');
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
    symlink($targetFolder, $linkFolder);
});

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

    // Student Contact
    Route::get('{id}/Contact', [StudentContactController::class, 'index'])->name('students.Contact');
    Route::post('{student_id}/storeContact', [StudentContactController::class, 'storeContact'])->name('students.StoreContact');
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
    Route::post('{employee_id}/storeContact', [EmployeeContactController::class, 'storeContact'])->name('employees.StoreContact');
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
    // Route Profile Details
    Route::get('list', [VendorProfileController::class, 'vendorlist'])->name('vendors.List');
    Route::get('List/All', [VendorProfileController::class, 'listAll'])->name('vendors.paginate');
    Route::get('{id}/Details', [VendorProfileController::class, 'Details'])->name('vendors.Details');
    Route::get('{id}/view/Details', [VendorProfileController::class, 'viewDetails'])->name('vendors.viewDetails');
    Route::get('{id}/Business', [VendorBusinessProfileController::class, 'Business'])->name('vendors.BusinessProfile');

    // Vendor Management
    Route::get('{id}/manage', [VendorController::class, 'manage'])->name('vendors.manage');
    Route::get('{id}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
    Route::post('{id}/update', [VendorController::class, 'update'])->name('vendors.update');

    // Vendor Business Info
    Route::get('{id}/BusinessInfo', [VendorBusinessProfileController::class, 'index'])->name('vendors.BusinessInfo');
    Route::get('{id}/{business_id}/BusinessDetails', [VendorBusinessProfileController::class, 'BusinessDetails'])->name('vendors.BusinessDetails');
    Route::get('{id}/Businesslist', [VendorBusinessProfileController::class, 'Businesslist'])->name('vendors.Businesslist');
    Route::get('{id}/all/Business', [VendorBusinessProfileController::class, 'allBusiness'])->name('vendors.AllBusiness');
    Route::post('/{id}/business/create', [VendorBusinessProfileController::class, 'addBusinessInfo']);

    // Business Profile Management
    Route::get('{id}/{business_id}/ManageBusinessinfo', [VendorBusinessProfileController::class, 'manage'])->name('vendors.manageBusinessinfo');
    Route::get('{id}/{business_id}/Business/Details', [VendorBusinessProfileController::class, 'fetchBusinessDetails'])->name('vendors.fetchBusinessDetails');
    Route::get('{id}/business/manage', [VendorBusinessProfileController::class, 'manageBusiness'])->name('vendors.ManageBusiness');
    Route::post('{id}/Business/Update', [VendorBusinessProfileController::class, 'updateBusiness'])->name('vendors.updateBusiness');

    // Individual Address Routes
    Route::get('{id}/Address', [VendorAddressController::class, 'index'])->name('vendors.Address');
    Route::get('{id}/Manage/Address', [VendorAddressController::class, 'manageAddress'])->name('vendors.addresses.manage');
    Route::get('{id}/Get/Address/List', [VendorAddressController::class, 'getAddresses'])->name('vendors.addresses.list');
    Route::get('{id}/addresses/{address_id}', [VendorAddressController::class, 'getAddress'])->name('vendors.addresses.get');
    Route::post('{id}/Manage/Addresses', [VendorAddressController::class, 'storeAddress'])->name('vendors.addresses.store');
    Route::post('{id}/{type}/addresses/{address_id}', [VendorAddressController::class, 'updateAddress'])->name('vendors.addresses.update');
    Route::delete('{id}/{type}/addresses/{address_id}', [VendorAddressController::class, 'deleteAddress'])->name('vendors.addresses.delete');
    Route::get('{id}/Address/Permanent', [VendorAddressController::class, 'permanentAddress'])->name('vendors.Address.Permanent');

    // Business Address Routes
    Route::get('{id}/{business_id}/Business/Address', [VendorAddressController::class, 'businessAddress'])->name('vendors.businessAddress');
    Route::get('{id}/{business_id}/Business/Address/list', [VendorAddressController::class, 'getBusinessAddresses'])->name('vendors.businessAddress.list');
    Route::get('{id}/{business_id}/Business/Address/{address_id}', [VendorAddressController::class, 'getBusinessAddress'])->name('vendors.businessAddress.get');
    Route::post('{id}/{business_id}/Business/Address/Add', [VendorAddressController::class, 'storeBusinessAddress'])->name('vendors.businessAddress.store');
    Route::put('{id}/{business_id}/Business/Address/{address_id}/Update', [VendorAddressController::class, 'updateBusinessAddress'])->name('vendors.businessAddress.update');
    Route::delete('{id}/{business_id}/Business/Address/{address_id}/Delete', [VendorAddressController::class, 'deleteBusinessAddress'])->name('vendors.businessAddress.delete');
    Route::get('{id}/{business_id}/Permanat/Business/Address/', [VendorAddressController::class, 'permanentBusinessAddress'])
        ->name('vendors.BusinessAddress.Permanent');

    // Vendor Business Contact 
    Route::get('{id}/{business_id}/BusinessContact/Permanent', [VendorContactController::class, 'permanentBusinessContact'])->name('vendors.BusinessContact');
    Route::get('{id}/{business_id}/Business/Contact', [VendorContactController::class, 'BusinessContact'])->name('vendors.BusinessContact');
    Route::get('{id}/{business_id}/Business/Contacts/List', [VendorContactController::class, 'getBusinessContacts'])->name('vendors.BusinessContacts.list');
    Route::get('{id}/{business_id}/Business/Contact/{contact_id}', [VendorContactController::class, 'getBusinessContact']);
    Route::post('{id}/{business_id}/Business/Contacts', [VendorContactController::class, 'addBusinessContact'])->name('vendors.BusinessContacts.store');
    Route::put('{id}/{business_id}/Business/Contacts/{contact_id}', [VendorContactController::class, 'updateBusinessContact'])->name('vendors.BusinessContacts.update');
    // Vendor individual Contact

    Route::get('{id}/Contact', [VendorContactController::class, 'index'])->name('vendors.Contact');
    Route::get('{id}/Manage/Contacts', [VendorContactController::class, 'manageContact'])->name('vendors.contacts.manageContact');
    Route::get('{id}/Get/Contacts', [VendorContactController::class, 'getContacts'])->name('vendors.contacts.list');
    Route::get('{vendor_id}/{type}/contacts/{contact_id}', [VendorContactController::class, 'getContact'])->name('vendors.contacts.get');
    Route::post('{vendor_id}/Manage/Contacts/Add', [VendorContactController::class, 'storeContact'])->name('vendors.contacts.store');
    Route::put('{vendor_id}/{type}/contacts/{contact_id}', [VendorContactController::class, 'updateContact'])->name('vendors.contacts.update');
    Route::delete('{vendor_id}/{type}/contacts/{contact_id}', [VendorContactController::class, 'deleteContact'])->name('vendors.contacts.delete');
    Route::get('{id}/Contact/Permanent', [VendorContactController::class, 'permanentContact'])->name('vendors.Contact.Permanent');

    // Vendor Bank

    // Individual Bank Routes
    Route::get('{id}/Bank', [VendorBankController::class, 'index'])->name('vendors.Bank');
    Route::post('{id}/saveBank', [VendorBankController::class, 'saveBank'])->name('vendors.Bank.saveBank');
    Route::get('{id}/BankList', [VendorBankController::class, 'vendorBanks'])->name('vendors.VendorBanks');
    Route::get('{id}/bank/{account_id}', [VendorBankController::class, 'fetchBank'])->name('vendors.bank.fetch');
    Route::put('{id}/updateBank/{account_id}', [VendorBankController::class, 'updateBank'])->name('vendors.bank.update');
    Route::delete('{id}/deleteBank/{account_id}', [VendorBankController::class, 'deleteBank'])->name('vendors.bank.delete');
    Route::get('{id}/Manage/Bank', [VendorBankController::class, 'ManageBank'])->name('vendors.ManageBank');
    Route::get('{id}/Permanent/BankDetails', [VendorBankController::class, 'permanentBank'])->name('vendors.permanentBank');

    // Business Bank Routes
    Route::get('{id}/{business_id}/Business/Bank', [VendorBankController::class, 'businessBank'])->name('vendors.businessBank');
    Route::get('{id}/{business_id}/business/BankList', [VendorBankController::class, 'vendorBusinessBank'])->name('vendors.vendorBusinessBank');
    Route::get('{id}/{business_id}/BusinessBank', [VendorBankController::class, 'permanentBusinessBank'])->name('vendors.BusinessBank');
    Route::post('{id}/{business_id}/business/saveBank', [VendorBankController::class, 'saveBank'])->name('vendors.business.saveBank'); // New
    Route::get('{id}/{business_id}/business/bank/{account_id}', [VendorBankController::class, 'fetchBank'])->name('vendors.business.bank.fetch'); // New
    Route::put('{id}/{business_id}/business/updateBank/{account_id}', [VendorBankController::class, 'updateBank'])->name('vendors.business.bank.update'); // New
    Route::delete('{id}/{business_id}/business/deleteBank/{account_id}', [VendorBankController::class, 'deleteBank'])->name('vendors.business.bank.delete'); // New Removed {type}

    Route::get('{vendor_id}/documents/add', [VendorDocumentController::class, 'index'])
        ->name('vendors.documents.add');

    Route::get('{vendor_id}/documents/business/{business_id}/add', [VendorDocumentController::class, 'BusinessDocs'])
        ->name('vendors.documents.business.add');

    /* Individual management page */


    /* Business management page */

    /*  (Individual Documents )  */
    Route::get('{vendor_id}/documents/individual/manage', [VendorDocumentController::class, 'manageIndividual'])->name('vendors.documents.individual.manage');
    Route::post('{vendor_id}/documents/individual/store',  [VendorDocumentController::class, 'storeIndividualDocument']);
    Route::get('{vendor_id}/documents/individual',        [VendorDocumentController::class, 'getIndividualDocuments']);
    Route::get('{vendor_id}/documents/individual/{doc_id}', [VendorDocumentController::class, 'getIndividualDocument']);
    Route::match(['put', 'patch'], '{vendor_id}/documents/individual/{doc_id}', [VendorDocumentController::class, 'updateIndividualDocument']);
    Route::delete('{vendor_id}/documents/individual/{doc_id}', [VendorDocumentController::class, 'deleteIndividualDocument']);


    /* (Business Documents)  */
    Route::post('{vendor_id}/{business_id}/documents/business/store', [VendorDocumentController::class, 'storeBusinessDocument']);
    Route::get('{vendor_id}/documents/business/{business_id}/manage', [VendorDocumentController::class, 'manageBusiness'])
        ->name('vendors.documents.business.manage');
    Route::get('{vendor_id}/documents/business/profile/{business_id}', [VendorDocumentController::class, 'businessDocuments'])
        ->name('vendors.documents.business.profile');
    Route::get('{vendor_id}/documents/business/{business_id}', [VendorDocumentController::class, 'getBusinessDocuments'])
        ->name('vendors.documents.business.index');
    Route::get('{vendor_id}/documents/business/{business_id}/{doc_id}', [VendorDocumentController::class, 'getBusinessDocument'])
        ->name('vendors.documents.business.show');
    Route::match(['put', 'patch'], '{vendor_id}/documents/business/{business_id}/{doc_id}', [VendorDocumentController::class, 'updateBusinessDocument'])
        ->name('vendors.documents.business.update');
    Route::delete('{vendor_id}/documents/business/{business_id}/{doc_id}', [VendorDocumentController::class, 'deleteBusinessDocument'])
        ->name('vendors.documents.business.destroy');


    // Vendor Media
    Route::get('{id}/media/index', [VendorMediaController::class, 'index']);
    Route::get('{id}/media/manage', [VendorMediaController::class, 'manage']);
    Route::get('{id}/{business_id}/media/business', [VendorMediaController::class, 'businessMedia']);

    // Individual Media CRUD
    Route::post('{vendor_id}/media/individual/store', [VendorMediaController::class, 'storeIndividualMedia']);
    Route::get('{vendor_id}/media/individual', [VendorMediaController::class, 'getIndividualMedias']);
    Route::get('{vendor_id}/media/individual/{media_id}', [VendorMediaController::class, 'getIndividualMedia']);
    Route::match(['put', 'patch'], '{vendor_id}/media/individual/{media_id}', [VendorMediaController::class, 'updateIndividualMedia']);
    Route::delete('{vendor_id}/media/individual/{media_id}', [VendorMediaController::class, 'deleteIndividualMedia']);

    // Business Media CRUD
    Route::post('{vendor_id}/{business_id}/media/business/store',[VendorMediaController::class, 'storeBusinessMedia'])->name('vendors.media.business.store');
    Route::get('{vendor_id}/{business_id}/media/business/List',[VendorMediaController::class, 'getBusinessMedias'])->name('vendors.media.business.index');
    Route::get('{vendor_id}/{business_id}/media/business/{media_id}',[VendorMediaController::class, 'getBusinessMedia'])->name('vendors.media.business.show');
    Route::match(['put', 'patch'],'{vendor_id}/{business_id}/media/business/{media_id}',[VendorMediaController::class, 'updateBusinessMedia'])->name('vendors.media.business.update');

    Route::delete('{vendor_id}/{business_id}/media/business/{media_id}',[VendorMediaController::class, 'deleteBusinessMedia'])->name('vendors.media.business.destroy');


    // Vendor Online Profile
    Route::get('{id}/OnlineProfile', [VendorOnlineProfileController::class, 'index'])->name('vendors.OnlineProfile');
    Route::post('{id}/storeOnlineProfile', [VendorOnlineProfileController::class, 'store'])->name('vendors.onlineProfiles.store');
    Route::get('{id}/{type}/OnlineProfile/Manage', [VendorOnlineProfileController::class, 'manage'])->name('vendors.onlineProfiles.manage');
    Route::get('{id}/{type}/OnlineProfile/List', [VendorOnlineProfileController::class, 'list'])->name('vendors.onlineProfiles.list');
    Route::post('{id}/{type}/OnlineProfile', [VendorOnlineProfileController::class, 'storeOne'])->name('vendors.onlineProfiles.storeOne');
    Route::put('{id}/{type}/OnlineProfile/{profile}', [VendorOnlineProfileController::class, 'update'])->name('vendors.onlineProfiles.update');
    Route::delete('{id}/{type}/OnlineProfile/{profile}', [VendorOnlineProfileController::class, 'destroy'])->name('vendors.onlineProfiles.destroy');
});

Route::prefix('customers/')->group(function () {

    Route::get('create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('store', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('list', [CustomerController::class, 'customerlist'])->name('customers.List');

    // Vendor Business Info
    Route::get('{id}/BusinessInfo', [CustomerBusinessProfileController::class, 'index'])->name('customers.BusinessInfo');

    // Customer Address 
    Route::get('{id}/Address', [CustomerAddressController::class, 'index'])->name('customers.Address');


    // Customer Contact 
    Route::get('{id}/Contact', [CustomerContactController::class, 'index'])->name('customers.Contact');

    // Customer Bank
    Route::get('{id}/Bank', [CustomerBankController::class, 'index'])->name('customers.Bank');

    // Customer Document
    Route::get('{id}/Document', [CustomerDocumentController::class, 'index'])->name('customers.Document');

    // Customer Media
    Route::get('{id}/Media', [CustomerMediaController::class, 'index'])->name('customers.Document');
});
