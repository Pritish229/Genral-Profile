<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendors\VendorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Education\CourseController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Education\SubjectController;
use App\Http\Controllers\Customers\CustomerController;
use App\Http\Controllers\Employees\EmployeeController;
use App\Http\Controllers\Vendors\VendorBankController;
use App\Http\Controllers\Vendors\VendorMediaController;
use App\Http\Controllers\Education\UniversityController;
use App\Http\Controllers\Students\StudentBankController;
use App\Http\Controllers\Education\CourseClassController;
use App\Http\Controllers\Education\SessionYearController;
use App\Http\Controllers\Students\StudentBasicController;
use App\Http\Controllers\Students\StudentMediaController;
use App\Http\Controllers\Vendors\VendorAddressController;
use App\Http\Controllers\Vendors\VendorContactController;
use App\Http\Controllers\Vendors\VendorProfileController;
use App\Http\Controllers\Customers\CustomerBankController;
use App\Http\Controllers\Education\ClassSectionController;
use App\Http\Controllers\Employees\EmployeeBankController;
use App\Http\Controllers\Vendors\VendorDocumentController;
use App\Http\Controllers\Customers\CustomerMediaController;
use App\Http\Controllers\Education\CollegeCourseController;
use App\Http\Controllers\Employees\EmployeeMediaController;
use App\Http\Controllers\FeeManagement\CourseFeeController;
use App\Http\Controllers\FeeManagement\FeemasterController;
use App\Http\Controllers\Students\StudentAddressController;
use App\Http\Controllers\Students\StudentContactController;
use App\Http\Controllers\Education\SubjectChapterController;
use App\Http\Controllers\FeeManagement\FeeSchduleController;
use App\Http\Controllers\FeeManagement\StudentFeeController;
use App\Http\Controllers\Customers\CustomerAddressController;
use App\Http\Controllers\Customers\CustomerContactController;
use App\Http\Controllers\Customers\CustomerProfileController;
use App\Http\Controllers\Employees\EmployeeAddressController;
use App\Http\Controllers\Employees\EmployeeContactController;
use App\Http\Controllers\Employees\EmployeePrimaryController;
use App\Http\Controllers\Students\StudentDocumentsController;
use App\Http\Controllers\Customers\CustomerDocumentController;
use App\Http\Controllers\Education\UniversityCourseController;
use App\Http\Controllers\Education\UniversityCollegeController;
use App\Http\Controllers\Employees\EmployeeDocumentsController;
use App\Http\Controllers\Vendors\VendorOnlineProfileController;
use App\Http\Controllers\Vendors\VendorBusinessProfileController;
use App\Http\Controllers\Education\CollegeCourseStudentController;
use App\Http\Controllers\Customers\CustomerOnlineProfileController;
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

    // Vendor Management
    Route::get('{id}/manage', [VendorController::class, 'manage'])->name('vendors.manage');
    Route::get('{id}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
    Route::post('{id}/update', [VendorController::class, 'update'])->name('vendors.update');


    // Business Profile Management
    Route::get('{id}/Business', [VendorBusinessProfileController::class, 'Business'])->name('vendors.BusinessProfile');
    Route::get('{id}/BusinessInfo', [VendorBusinessProfileController::class, 'index'])->name('vendors.BusinessInfo');
    Route::get('/AllBusiness', [VendorBusinessProfileController::class, 'allBusinessPage'])->name('vendors.allBusinessPage');
    Route::post('/{id}/business/create', [VendorBusinessProfileController::class, 'addBusinessInfo']);
    Route::get('{id}/{business_id}/BusinessDetails', [VendorBusinessProfileController::class, 'BusinessDetails'])->name('vendors.BusinessDetails');
    Route::get('{id}/Businesslist', [VendorBusinessProfileController::class, 'Businesslist'])->name('vendors.Businesslist');
    Route::get('{id}/all/Business', [VendorBusinessProfileController::class, 'allBusiness'])->name('vendors.AllBusiness');
    Route::get('/vendor-businesses/data', [VendorBusinessProfileController::class, 'totalBusiness'])->name('vendors.totalBusiness');
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
    Route::post('{id}/{business_id}/Business/Address/{address_id}/Update', [VendorAddressController::class, 'updateBusinessAddress'])->name('vendors.businessAddress.update');
    Route::delete('{id}/{business_id}/Business/Address/{address_id}/Delete', [VendorAddressController::class, 'deleteBusinessAddress'])->name('vendors.businessAddress.delete');
    Route::get('{id}/{business_id}/Permanat/Business/Address/', [VendorAddressController::class, 'permanentBusinessAddress'])
        ->name('vendors.BusinessAddress.Permanent');

    // Vendor Business Contact 
    Route::get('{id}/{business_id}/BusinessContact/Permanent', [VendorContactController::class, 'permanentBusinessContact'])->name('vendors.BusinessContact');
    Route::get('{id}/{business_id}/Business/Contact', [VendorContactController::class, 'BusinessContact'])->name('vendors.BusinessContact');
    Route::get('{id}/{business_id}/Business/Contacts/List', [VendorContactController::class, 'getBusinessContacts'])->name('vendors.BusinessContacts.list');
    Route::get('{id}/{business_id}/Business/Contact/{contact_id}', [VendorContactController::class, 'getBusinessContact']);
    Route::post('{id}/{business_id}/Business/Contacts', [VendorContactController::class, 'addBusinessContact'])->name('vendors.BusinessContacts.store');
    Route::post('{id}/{business_id}/Business/Contacts/{contact_id}', [VendorContactController::class, 'updateBusinessContact'])->name('vendors.BusinessContacts.update');

    // Vendor individual Contact
    Route::get('{id}/Contact', [VendorContactController::class, 'index'])->name('vendors.Contact');
    Route::get('{id}/Manage/Contacts', [VendorContactController::class, 'manageContact'])->name('vendors.contacts.manageContact');
    Route::get('{id}/Get/Contacts', [VendorContactController::class, 'getContacts'])->name('vendors.contacts.list');
    Route::get('{vendor_id}/{type}/contacts/{contact_id}', [VendorContactController::class, 'getContact'])->name('vendors.contacts.get');
    Route::post('{vendor_id}/Manage/Contacts/Add', [VendorContactController::class, 'storeContact'])->name('vendors.contacts.store');
    Route::post('{vendor_id}/{type}/contacts/{contact_id}', [VendorContactController::class, 'updateContact'])->name('vendors.contacts.update');
    Route::delete('{vendor_id}/{type}/contacts/{contact_id}', [VendorContactController::class, 'deleteContact'])->name('vendors.contacts.delete');
    Route::get('{id}/Contact/Permanent', [VendorContactController::class, 'permanentContact'])->name('vendors.Contact.Permanent');

    // Vendor Bank

    // Individual Bank Routes
    Route::get('{id}/Bank', [VendorBankController::class, 'index'])->name('vendors.Bank');
    Route::post('{id}/saveBank', [VendorBankController::class, 'saveBank'])->name('vendors.Bank.saveBank');
    Route::get('{id}/BankList', [VendorBankController::class, 'vendorBanks'])->name('vendors.VendorBanks');
    Route::get('{id}/bank/{account_id}', [VendorBankController::class, 'fetchBank'])->name('vendors.bank.fetch');
    Route::post('{id}/updateBank/{account_id}', [VendorBankController::class, 'updateBank'])->name('vendors.bank.update');
    Route::delete('{id}/deleteBank/{account_id}', [VendorBankController::class, 'deleteBank'])->name('vendors.bank.delete');
    Route::get('{id}/Manage/Bank', [VendorBankController::class, 'ManageBank'])->name('vendors.ManageBank');
    Route::get('{id}/Permanent/BankDetails', [VendorBankController::class, 'permanentBank'])->name('vendors.permanentBank');

    // Business Bank Routes
    Route::get('{id}/{business_id}/Business/Bank', [VendorBankController::class, 'businessBank'])->name('vendors.businessBank');
    Route::get('{id}/{business_id}/business/BankList', [VendorBankController::class, 'vendorBusinessBank'])->name('vendors.vendorBusinessBank');
    Route::get('{id}/{business_id}/BusinessBank', [VendorBankController::class, 'permanentBusinessBank'])->name('vendors.BusinessBank');
    Route::post('{id}/{business_id}/business/saveBank', [VendorBankController::class, 'saveBank'])->name('vendors.business.saveBank'); // New
    Route::get('{vendor_id}/banks/{account_id}/{business_id?}', [VendorBankController::class, 'fetchBank'])->name('vendors.bank.fetch');
    Route::post('{id}/{business_id}/business/updateBank/{account_id}', [VendorBankController::class, 'updateBank'])->name('vendors.business.bank.update'); // New
    Route::delete('{id}/{business_id}/business/deleteBank/{account_id}', [VendorBankController::class, 'deleteBank'])->name('vendors.business.bank.delete'); // New Removed {type}
    Route::get('{vendor_id}/documents/add', [VendorDocumentController::class, 'index'])->name('vendors.documents.add');
    Route::get('{vendor_id}/documents/business/{business_id}/add', [VendorDocumentController::class, 'BusinessDocs'])->name('vendors.documents.business.add');


    /*  (Individual Documents )  */
    Route::get('{vendor_id}/Document', [VendorDocumentController::class, 'index'])->name('vendors.documents.index');
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
    Route::get('{id}/Media', [VendorMediaController::class, 'index']);
    Route::get('{id}/media/manage', [VendorMediaController::class, 'manage']);
    Route::get('{id}/{business_id}/media/business', [VendorMediaController::class, 'businessMedia']);

    // Individual Media CRUD
    Route::post('{vendor_id}/media/individual/store', [VendorMediaController::class, 'storeIndividualMedia']);
    Route::get('{vendor_id}/media/individual', [VendorMediaController::class, 'getIndividualMedias']);
    Route::get('{vendor_id}/media/individual/{media_id}', [VendorMediaController::class, 'getIndividualMedia']);
    Route::match(['put', 'patch'], '{vendor_id}/media/individual/{media_id}', [VendorMediaController::class, 'updateIndividualMedia']);
    Route::delete('{vendor_id}/media/individual/{media_id}', [VendorMediaController::class, 'deleteIndividualMedia']);

    // Business Media CRUD
    Route::post('{vendor_id}/{business_id}/media/business/store', [VendorMediaController::class, 'storeBusinessMedia'])->name('vendors.media.business.store');
    Route::get('{vendor_id}/{business_id}/media/business/List', [VendorMediaController::class, 'getBusinessMedias'])->name('vendors.media.business.index');
    Route::get('{vendor_id}/{business_id}/media/business/{media_id}', [VendorMediaController::class, 'getBusinessMedia'])->name('vendors.media.business.show');
    Route::match(['put', 'patch'], '{vendor_id}/{business_id}/media/business/{media_id}', [VendorMediaController::class, 'updateBusinessMedia'])->name('vendors.media.business.update');
    Route::delete('{vendor_id}/{business_id}/media/business/{media_id}', [VendorMediaController::class, 'deleteBusinessMedia'])->name('vendors.media.business.destroy');

    // Individual Online Profile
    Route::get('{id}/individual/OnlineProfile', [VendorOnlineProfileController::class, 'individualIndex'])->name('vendors.individual.add');
    Route::post('{id}/individual/storeOnlineProfile', [VendorOnlineProfileController::class, 'individualStore'])->name('vendors.individual.store');
    Route::get('{id}/individual/OnlineProfile/Manage', [VendorOnlineProfileController::class, 'individualManage'])->name('vendors.individual.manage');
    Route::get('{id}/individual/OnlineProfile/List', [VendorOnlineProfileController::class, 'individualList'])->name('vendors.individual.list');
    Route::post('{id}/individual/OnlineProfile/Add', [VendorOnlineProfileController::class, 'individualStoreOne'])->name('vendors.individual.storeOne');
    Route::put('{id}/individual/OnlineProfile/{profile}', [VendorOnlineProfileController::class, 'individualUpdate'])->name('vendors.individual.update');
    Route::delete('{id}/individual/OnlineProfile/{profile}', [VendorOnlineProfileController::class, 'individualDestroy'])->name('vendors.individual.destroy');

    // Business Online Profile
    Route::get('{id}/business/OnlineProfile/{business_id}', [VendorOnlineProfileController::class, 'businessIndex'])->name('customers.business.add');
    Route::post('{id}/business/storeOnlineProfile/{business_id}', [VendorOnlineProfileController::class, 'businessStore'])->name('customers.business.store');
    Route::get('{id}/business/OnlineProfile/Manage', [VendorOnlineProfileController::class, 'businessManage'])->name('customers.business.manage');
    Route::get('{id}/{business_id}/business/OnlineProfile/List', [VendorOnlineProfileController::class, 'businessList'])->name('customers.business.list');
    Route::post('{id}/business/OnlineProfile', [VendorOnlineProfileController::class, 'businessStoreOne'])->name('customers.business.storeOne');
    Route::put('{id}/business/OnlineProfile/{profile}', [VendorOnlineProfileController::class, 'businessUpdate'])->name('customers.business.update');
    Route::delete('{id}/business/OnlineProfile/{profile}', [VendorOnlineProfileController::class, 'businessDestroy'])->name('customers.business.destroy');
});

Route::prefix('customers/')->group(function () {

    Route::get('create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('store', [CustomerController::class, 'store'])->name('customers.store');

    // Route Profile Details
    Route::get('profile/list', [CustomerProfileController::class, 'customerlist'])->name('customers.List');
    Route::get('List/All', [CustomerProfileController::class, 'listAll'])->name('customers.paginate');
    Route::get('{id}/Details', [CustomerProfileController::class, 'Details'])->name('customers.Details');
    Route::get('{id}/view/Details', [CustomerProfileController::class, 'viewDetails'])->name('customers.viewDetails');


    // Customer Management
    Route::get('{id}/manage', [CustomerController::class, 'manage'])->name('customers.manage');
    Route::get('{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::post('{id}/update', [CustomerController::class, 'update'])->name('customers.update');

    // Business Profile Management
    Route::get('{id}/Business', [CustomerBusinessProfileController::class, 'Business'])->name('customers.BusinessProfile');
    Route::get('{id}/BusinessInfo', [CustomerBusinessProfileController::class, 'index'])->name('customers.BusinessInfo');
    Route::get('/AllBusiness', [CustomerBusinessProfileController::class, 'allBusinessPage'])->name('customers.allBusinessPage');
    Route::post('/{id}/business/create', [CustomerBusinessProfileController::class, 'addBusinessInfo']);
    Route::get('{id}/{business_id}/BusinessDetails', [CustomerBusinessProfileController::class, 'BusinessDetails'])->name('customers.BusinessDetails');
    Route::get('{id}/Businesslist', [CustomerBusinessProfileController::class, 'Businesslist'])->name('customers.Businesslist');
    Route::get('{id}/all/Business', [CustomerBusinessProfileController::class, 'allBusiness'])->name('customers.AllBusiness');
    Route::get('/customer-businesses/data', [CustomerBusinessProfileController::class, 'totalBusiness'])->name('customers.totalBusiness');
    Route::get('{id}/{business_id}/ManageBusinessinfo', [CustomerBusinessProfileController::class, 'manage'])->name('customers.manageBusinessinfo');
    Route::get('{id}/{business_id}/Business/Details', [CustomerBusinessProfileController::class, 'fetchBusinessDetails'])->name('customers.fetchBusinessDetails');
    Route::get('{id}/business/manage', [CustomerBusinessProfileController::class, 'manageBusiness'])->name('customers.ManageBusiness');
    Route::post('{id}/Business/Update', [CustomerBusinessProfileController::class, 'updateBusiness'])->name('customers.updateBusiness');

    // Individual Address Routes
    Route::get('{id}/Address', [CustomerAddressController::class, 'index'])->name('customers.Address');
    Route::get('{id}/Manage/Address', [CustomerAddressController::class, 'manageAddress'])->name('customers.addresses.manage');
    Route::get('{id}/Get/Address/List', [CustomerAddressController::class, 'getAddresses'])->name('customers.addresses.list');
    Route::get('{id}/addresses/{address_id}', [CustomerAddressController::class, 'getAddress'])->name('customers.addresses.get');
    Route::post('{id}/Manage/Addresses', [CustomerAddressController::class, 'storeAddress'])->name('customers.addresses.store');
    Route::post('{id}/{type}/addresses/{address_id}', [CustomerAddressController::class, 'updateAddress'])->name('customers.addresses.update');
    Route::delete('{id}/{type}/addresses/{address_id}', [CustomerAddressController::class, 'deleteAddress'])->name('customers.addresses.delete');
    Route::get('{id}/Address/Permanent', [CustomerAddressController::class, 'permanentAddress'])->name('customers.Address.Permanent');

    // Business Address Routes
    Route::get('{id}/{business_id}/Business/Address', [CustomerAddressController::class, 'businessAddress'])->name('customers.businessAddress');
    Route::get('{id}/{business_id}/Business/Address/list', [CustomerAddressController::class, 'getBusinessAddresses'])->name('customers.businessAddress.list');
    Route::get('{id}/{business_id}/Business/Address/{address_id}', [CustomerAddressController::class, 'getBusinessAddress'])->name('customers.businessAddress.get');
    Route::post('{id}/{business_id}/Business/Address/Add', [CustomerAddressController::class, 'storeBusinessAddress'])->name('customers.businessAddress.store');
    Route::post('{id}/{business_id}/Business/Address/{address_id}/Update', [CustomerAddressController::class, 'updateBusinessAddress'])->name('customers.businessAddress.update');
    Route::delete('{id}/{business_id}/Business/Address/{address_id}/Delete', [CustomerAddressController::class, 'deleteBusinessAddress'])->name('customers.businessAddress.delete');
    Route::get('{id}/{business_id}/Permanat/Business/Address/', [CustomerAddressController::class, 'permanentBusinessAddress'])
        ->name('customers.BusinessAddress.Permanent');


    // Customer Business Contact 
    Route::get('{id}/{business_id}/BusinessContact/Permanent', [CustomerContactController::class, 'permanentBusinessContact'])->name('customers.BusinessContact');
    Route::get('{id}/{business_id}/Business/Contact', [CustomerContactController::class, 'BusinessContact'])->name('customers.Business.Contact');
    Route::get('{id}/{business_id}/Business/Contacts/List', [CustomerContactController::class, 'getBusinessContacts'])->name('customers.BusinessContacts.list');
    Route::get('{id}/{business_id}/Business/Contact/{contact_id}', [CustomerContactController::class, 'getBusinessContact']);
    Route::post('{id}/{business_id}/Business/Contacts', [CustomerContactController::class, 'addBusinessContact'])->name('customers.BusinessContacts.store');
    Route::post('{id}/{business_id}/Business/Contacts/{contact_id}', [CustomerContactController::class, 'updateBusinessContact'])->name('customers.BusinessContacts.update');

    // Customer individual Contact
    Route::get('{id}/Contact', [CustomerContactController::class, 'index'])->name('customers.Contact');
    Route::get('{id}/Manage/Contacts', [CustomerContactController::class, 'manageContact'])->name('customers.contacts.manageContact');
    Route::get('{id}/Get/Contacts', [CustomerContactController::class, 'getContacts'])->name('customers.contacts.list');
    Route::get('{customer_id}/{type}/contacts/{contact_id}', [CustomerContactController::class, 'getContact'])->name('customers.contacts.get');
    Route::post('{customer_id}/Manage/Contacts/Add', [CustomerContactController::class, 'storeContact'])->name('customers.contacts.store');
    Route::post('{customer_id}/{type}/contacts/{contact_id}', [CustomerContactController::class, 'updateContact'])->name('customers.contacts.update');
    Route::delete('{customer_id}/{type}/contacts/{contact_id}', [CustomerContactController::class, 'deleteContact'])->name('customers.contacts.delete');
    Route::get('{id}/Contact/Permanent', [CustomerContactController::class, 'permanentContact'])->name('customers.Contact.Permanent');


    // Individual Bank Routes
    Route::get('{id}/Bank', [CustomerBankController::class, 'index'])->name('customers.Bank');
    Route::post('{id}/saveBank', [CustomerBankController::class, 'saveBank'])->name('customers.Bank.saveBank');
    Route::get('{id}/BankList', [CustomerBankController::class, 'customerBanks'])->name('customers.CustomerBanks');
    Route::get('{id}/bank/{account_id}', [CustomerBankController::class, 'fetchBank'])->name('customers.bank.fetch');
    Route::post('{id}/updateBank/{account_id}', [CustomerBankController::class, 'updateBank'])->name('customers.bank.update');
    Route::delete('{id}/deleteBank/{account_id}', [CustomerBankController::class, 'deleteBank'])->name('customers.bank.delete');
    Route::get('{id}/Manage/Bank', [CustomerBankController::class, 'ManageBank'])->name('customers.ManageBank');
    Route::get('{id}/Permanent/BankDetails', [CustomerBankController::class, 'permanentBank'])->name('customers.permanentBank');

    // Business Bank Routes
    Route::get('{id}/{business_id}/Business/Bank', [CustomerBankController::class, 'businessBank'])->name('customers.businessBank');
    Route::get('{id}/{business_id}/business/BankList', [CustomerBankController::class, 'customerBusinessBank'])->name('customers.CustomerBusinessBank');
    Route::get('{id}/{business_id}/BusinessBank', [CustomerBankController::class, 'permanentBusinessBank'])->name('customers.BusinessBank');
    Route::post('{id}/{business_id}/business/saveBank', [CustomerBankController::class, 'saveBank'])->name('customers.business.saveBank'); // New
    Route::get('{id}/{business_id}/business/bank/{account_id}', [CustomerBankController::class, 'fetchBank'])->name('customers.business.bank.fetch'); // New
    Route::post('{id}/{business_id}/{account_id}/business/updateBank/', [CustomerBankController::class, 'updateBank'])->name('customers.business.bank.update'); // New
    Route::delete('{id}/{business_id}/business/deleteBank/{account_id}', [CustomerBankController::class, 'deleteBank'])->name('customers.business.bank.delete'); // New Removed {type}


    /*  (Individual Documents )  */
    Route::get('{customer_id}/documents/add', [CustomerDocumentController::class, 'index'])->name('customers.documents.add');
    Route::get('{customer_id}/Document', [CustomerDocumentController::class, 'index'])->name('customers.documents.index');
    Route::get('{customer_id}/documents/individual/manage', [CustomerDocumentController::class, 'manageIndividual'])->name('customers.documents.individual.manage');
    Route::post('{customer_id}/documents/individual/store',  [CustomerDocumentController::class, 'storeIndividualDocument']);
    Route::get('{customer_id}/documents/individual',        [CustomerDocumentController::class, 'getIndividualDocuments']);
    Route::get('{customer_id}/documents/individual/{doc_id}', [CustomerDocumentController::class, 'getIndividualDocument']);
    Route::match(['put', 'patch'], '{customer_id}/documents/individual/{doc_id}', [CustomerDocumentController::class, 'updateIndividualDocument']);
    Route::delete('{customer_id}/documents/individual/{doc_id}', [CustomerDocumentController::class, 'deleteIndividualDocument']);

    /* (Business Documents)  */
    Route::get('{customer_id}/documents/business/{business_id}/add', [CustomerDocumentController::class, 'BusinessDocs'])->name('customers.documents.business.add');
    Route::post('{customer_id}/{business_id}/documents/business/store', [CustomerDocumentController::class, 'storeBusinessDocument']);
    Route::get('{customer_id}/documents/business/{business_id}/manage', [CustomerDocumentController::class, 'manageBusiness'])->name('customers.documents.business.manage');
    Route::get('{customer_id}/documents/business/profile/{business_id}', [CustomerDocumentController::class, 'businessDocuments'])->name('customers.documents.business.profile');
    Route::get('{customer_id}/documents/business/{business_id}', [CustomerDocumentController::class, 'getBusinessDocuments'])->name('customers.documents.business.index');
    Route::get('{customer_id}/documents/business/{business_id}/{doc_id}', [CustomerDocumentController::class, 'getBusinessDocument'])->name('customers.documents.business.show');
    Route::match(['put', 'patch'], '{customer_id}/documents/business/{business_id}/{doc_id}', [CustomerDocumentController::class, 'updateBusinessDocument'])->name('customers.documents.business.update');
    Route::delete('{customer_id}/documents/business/{business_id}/{doc_id}', [CustomerDocumentController::class, 'deleteBusinessDocument'])->name('customers.documents.business.destroy');

    // Customer Media
    Route::get('{id}/Media', [CustomerMediaController::class, 'index']);
    Route::get('{id}/media/manage', [CustomerMediaController::class, 'manage']);
    Route::get('{id}/{business_id}/media/business', [CustomerMediaController::class, 'businessMedia']);

    // Individual Media CRUD
    Route::post('{customer_id}/media/individual/store', [CustomerMediaController::class, 'storeIndividualMedia']);
    Route::get('{customer_id}/media/individual', [CustomerMediaController::class, 'getIndividualMedias']);
    Route::get('{customer_id}/media/individual/{media_id}', [CustomerMediaController::class, 'getIndividualMedia']);
    Route::match(['put', 'patch'], '{customer_id}/media/individual/{media_id}', [CustomerMediaController::class, 'updateIndividualMedia']);
    Route::delete('{customer_id}/media/individual/{media_id}', [CustomerMediaController::class, 'deleteIndividualMedia']);

    // Business Media CRUD
    Route::post('{customer_id}/{business_id}/media/business/store', [CustomerMediaController::class, 'storeBusinessMedia'])->name('customers.media.business.store');
    Route::get('{customer_id}/{business_id}/media/business/List', [CustomerMediaController::class, 'getBusinessMedias'])->name('customers.media.business.index');
    Route::get('{customer_id}/{business_id}/media/business/{media_id}', [CustomerMediaController::class, 'getBusinessMedia'])->name('customers.media.business.show');
    Route::match(['put', 'patch'], '{customer_id}/{business_id}/media/business/{media_id}', [CustomerMediaController::class, 'updateBusinessMedia'])->name('customers.media.business.update');
    Route::delete('{customer_id}/{business_id}/media/business/{media_id}', [CustomerMediaController::class, 'deleteBusinessMedia'])->name('customers.media.business.destroy');


    // Individual Online Profile
    Route::get('{id}/individual/OnlineProfile', [CustomerOnlineProfileController::class, 'individualIndex'])->name('customers.individual.add');
    Route::post('{id}/individual/storeOnlineProfile', [CustomerOnlineProfileController::class, 'individualStore'])->name('customers.individual.store');
    Route::get('{id}/individual/OnlineProfile/Manage', [CustomerOnlineProfileController::class, 'individualManage'])->name('customers.individual.manage');
    Route::get('{id}/individual/OnlineProfile/List', [CustomerOnlineProfileController::class, 'individualList'])->name('customers.individual.list');
    Route::post('{id}/individual/OnlineProfile', [CustomerOnlineProfileController::class, 'individualStoreOne'])->name('customers.individual.storeOne');
    Route::put('{id}/individual/OnlineProfile/{profile}', [CustomerOnlineProfileController::class, 'individualUpdate'])->name('customers.individual.update');
    Route::delete('{id}/individual/OnlineProfile/{profile}', [CustomerOnlineProfileController::class, 'individualDestroy'])->name('customers.individual.destroy');
    // Business Online Profile
    Route::get('{id}/business/OnlineProfile/{business_id}', [CustomerOnlineProfileController::class, 'businessIndex'])->name('business.add');
    Route::post('{id}/business/storeOnlineProfile/{business_id}', [CustomerOnlineProfileController::class, 'businessStore'])->name('business.store');
    Route::get('{id}/business/OnlineProfile/Manage', [CustomerOnlineProfileController::class, 'businessManage'])->name('business.manage');
    Route::get('{id}/{business_id}/business/OnlineProfile/List', [CustomerOnlineProfileController::class, 'businessList'])->name('business.list');
    Route::post('{id}/business/OnlineProfile', [CustomerOnlineProfileController::class, 'businessStoreOne'])->name('business.storeOne');
    Route::put('{id}/business/OnlineProfile/{profile}', [CustomerOnlineProfileController::class, 'businessUpdate'])->name('business.update');
    Route::delete('{id}/business/OnlineProfile/{profile}', [CustomerOnlineProfileController::class, 'businessDestroy'])->name('business.destroy');
});


Route::prefix('education/')->group(function () {

    Route::get('university/master', [UniversityController::class, 'index'])->name('education.university.index');
    Route::post('university/master', [UniversityController::class, 'store'])->name('education.university.store');
    Route::get('university/master/list', [UniversityController::class, 'list'])->name('education.university.list');
    Route::get('university/master/show/{id}', [UniversityController::class, 'show'])->name('education.university.show');
    Route::get('university/master/allUniversities', [UniversityController::class, 'allUniversities'])->name('education.university.allUniversities');
    Route::post('university/master/update/{id}', [UniversityController::class, 'update'])->name('education.university.update');


    Route::get('university/Courses/{id}', [UniversityCourseController::class, 'index'])->name('education.universitycourse.index');
    Route::get('/paginate', [UniversityCourseController::class, 'paginate'])->name('education.universitycourse.paginate');
    Route::post('/store', [UniversityCourseController::class, 'assign'])->name('education.universitycourse.store');
    Route::get('/edit/{id}', [UniversityCourseController::class, 'edit'])->name('education.universitycourse.edit');
    Route::post('/update/{id}', [UniversityCourseController::class, 'update'])->name('education.universitycourse.update');
    Route::delete('/delete/{id}', [UniversityCourseController::class, 'destroy'])->name('education.universitycourse.delete');
    Route::post('/restore/{university}/{id}', [UniversityCourseController::class, 'restore'])->name('education.universitycourse.restore');
    Route::get('/university-course/deleted/{university}', [UniversityCourseController::class, 'getDeletedCourses'])->name('education.universitycourse.deleted');
    Route::get('/university-course/list/{university}', [UniversityCourseController::class, 'getUniversityCourses'])->name('education.universitycourse.getUniversityCourses');
    Route::get('/university-course/childlist/{university}/{id}', [UniversityCourseController::class, 'childCourses'])->name('education.universitycourse.childlist');

    Route::get('university-colleges', [UniversityCollegeController::class, 'index'])->name('education.college.index');
    Route::post('university-colleges', [UniversityCollegeController::class, 'store'])->name('education.college.store');
    Route::get('university-colleges/{college}/edit', [UniversityCollegeController::class, 'edit'])->name('education.college.edit');
    Route::post('university-colleges/{college}', [UniversityCollegeController::class, 'update'])->name('education.college.update');
    Route::delete('university-colleges/{college}', [UniversityCollegeController::class, 'destroy'])->name('education.college.destroy');
    Route::get('university-colleges/list', [UniversityCollegeController::class, 'list'])->name('education.college.list');


    Route::get('college-courses/{college}/{university}', [CollegeCourseController::class, 'index'])->name('collegecourse.index');
    Route::get('/college/list', [CollegeCourseController::class, 'listColleges'])->name('education.collegecourse.list');
    Route::get('/college-course/list/{college}', [CollegeCourseController::class, 'getCollegeCourses'])->name('education.collegecourse.getCourses');
    Route::post('/college-course/assign/{college}', [CollegeCourseController::class, 'assignCourses'])->name('education.collegecourse.assign');
    Route::get('college-courses/Students', [CollegeCourseStudentController::class, 'index'])->name('education.coursestudent.index');



    Route::get('/session-years', [SessionYearController::class, 'index'])->name('education.sessionyear.index');
    Route::get('/session-years/Pagenate', [SessionYearController::class, 'sessionPaginate'])->name('education.sessionyear.sessionPaginate');
    Route::get('/session-years/list', [SessionYearController::class, 'list'])->name('education.sessionyear.list');
    Route::post('/session-years/store', [SessionYearController::class, 'store'])->name('education.sessionyear.store');
    Route::get('/session-years/{id}/edit', [SessionYearController::class, 'edit'])->name('education.sessionyear.edit');
    Route::post('/session-years/{id}/update', [SessionYearController::class, 'update'])->name('education.sessionyear.update');
    Route::delete('/session-years/{id}/delete', [SessionYearController::class, 'destroy'])->name('education.sessionyear.delete');
    Route::get('/session-years/active', [SessionYearController::class, 'activeSessions'])->name('education.sessionyear.active');
    Route::get('/session-years/inactive', [SessionYearController::class, 'inactiveSessions'])->name('education.sessionyear.inactive');

    Route::get('/courses', [CourseController::class, 'index'])->name('education.course.index');
    Route::get('/courses/paginate', [CourseController::class, 'paginate'])->name('education.course.paginate');
    Route::post('/courses/store', [CourseController::class, 'store'])->name('education.course.store');
    Route::post('/courses/{id}/restore', [CourseController::class, 'restore'])->name('education.course.restore');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('education.course.edit');
    Route::post('/courses/{id}/update', [CourseController::class, 'update'])->name('education.course.update');
    Route::delete('/courses/{id}/delete', [CourseController::class, 'destroy'])->name('education.course.delete');
    Route::get('/session-courses/list', [CourseController::class, 'SessionWiseCourselist'])->name('education.course.SessionWise');
    Route::get('/courses/list-all', [CourseController::class, 'listAll'])->name('education.course.listAll');
    Route::get('/courses/parent/list-all', [CourseController::class, 'parentCourses'])->name('education.course.parentCourses');
    Route::get('/courses/child/{id}', [CourseController::class, 'childCourses'])->name('education.course.childCourses');

    Route::get('classes', [CourseClassController::class, 'index'])->name('education.class.index');
    Route::get('classes/paginate', [CourseClassController::class, 'paginate'])->name('education.class.paginate');
    Route::post('classes/store', [CourseClassController::class, 'store'])->name('education.class.store');
    Route::get('classes/{id}/edit', [CourseClassController::class, 'edit'])->name('education.class.edit');
    Route::get('course-classes/list', [CourseClassController::class, 'getCoursesBySessionYear'])->name('education.class.CourseWise');
    Route::post('classes/{id}/update', [CourseClassController::class, 'update'])->name('education.class.update');
    Route::delete('classes/{id}/delete', [CourseClassController::class, 'destroy'])->name('education.class.delete');

    Route::get('subjects', [SubjectController::class, 'index'])->name('education.subject.index');
    Route::get('subjects/paginate', [SubjectController::class, 'paginate'])->name('education.subject.paginate');
    Route::post('subjects', [SubjectController::class, 'store'])->name('education.subject.store');
    Route::get('subjects/{id}/edit', [SubjectController::class, 'edit'])->name('education.subject.edit');
    Route::get('class-subjects/list', [SubjectController::class, 'listSubjectsByClass'])->name('education.subject.ClassWise');
    Route::post('subjects/{id}', [SubjectController::class, 'update'])->name('education.subject.update');
    Route::delete('subjects/{id}', [SubjectController::class, 'destroy'])->name('education.subject.delete');

    Route::get('/chapters', [SubjectChapterController::class, 'index'])->name('education.chapter.index');
    Route::get('/chapters/paginate', [SubjectChapterController::class, 'paginate'])->name('education.chapter.paginate');
    Route::post('/chapters/store', [SubjectChapterController::class, 'store'])->name('education.chapter.store');
    Route::get('/chapters/{id}/edit', [SubjectChapterController::class, 'edit'])->name('education.chapter.edit');
    Route::post('/chapters/{id}/update', [SubjectChapterController::class, 'update'])->name('education.chapter.update');
    Route::delete('/chapters/{id}/delete', [SubjectChapterController::class, 'destroy'])->name('education.chapter.delete');

    Route::get('section', [ClassSectionController::class, 'index'])->name('education.section.index');
    Route::get('section/paginate', [ClassSectionController::class, 'paginate'])->name('education.section.paginate');
    Route::post('section/store', [ClassSectionController::class, 'store'])->name('education.section.store');
    Route::get('section/edit/{id}', [ClassSectionController::class, 'edit'])->name('education.section.edit');
    Route::post('section/update/{id}', [ClassSectionController::class, 'update'])->name('education.section.update');
    Route::delete('section/destroy/{id}', [ClassSectionController::class, 'destroy'])->name('education.section.destroy');
});

Route::prefix('fees')->group(function () {


    Route::get('Students-Fees', [StudentFeeController::class, 'index'])->name('fee.studentfee.index');
    Route::get('Students-Fees/Schdule', [FeeSchduleController::class, 'index'])->name('fee.FeeSchdule.index');
    // FEE MASTER
    Route::get('/master', [FeeMasterController::class, 'index'])->name('fee.feemaster.index');
    Route::get('/master/paginate', [FeeMasterController::class, 'paginate'])->name('fee.feemaster.paginate');
    Route::post('/master/store', [FeeMasterController::class, 'store'])->name('fee.feemaster.store');
    Route::get('/master/list', [FeeMasterController::class, 'feelist'])->name('fee.feemaster.list');
    Route::get('/master/edit/{id}', [FeeMasterController::class, 'edit'])->name('fee.feemaster.edit');
    Route::post('/master/update/{id}', [FeeMasterController::class, 'update'])->name('fee.feemaster.update');
    Route::delete('/master/delete/{id}', [FeeMasterController::class, 'delete'])->name('fee.feemaster.delete');

    // COURSE FEE
    Route::get('/', [CourseFeeController::class, 'index'])->name('coursefee.index');
    Route::get('/course/paginate', [CourseFeeController::class, 'paginate'])->name('coursefee.paginate');
    Route::get('/course/list', [CourseFeeController::class, 'list'])->name('coursefee.list');
    Route::post('/course/store', [CourseFeeController::class, 'store'])->name('coursefee.store');
    Route::get('/course/details/{id}', [CourseFeeController::class, 'details'])->name('coursefee.details');
    Route::get('/course/edit/{id}', [CourseFeeController::class, 'edit'])->name('coursefee.edit');
    Route::post('/course/update/{id}', [CourseFeeController::class, 'update'])->name('coursefee.update');
    Route::delete('/course/delete/{id}', [CourseFeeController::class, 'delete'])->name('coursefee.delete');
});
