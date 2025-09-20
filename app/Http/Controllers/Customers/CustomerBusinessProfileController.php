<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerBusinessProfileController extends Controller
{
    public function customerList()
    {
        return view('Admin.Customers.StudentProfile.StudentList',);
    }
}
