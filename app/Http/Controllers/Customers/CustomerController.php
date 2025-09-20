<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function create()
    {
        return view('Admin.Customers.CustomerProfile.AddCustomer');
    }

    public function customerlist()
    {
        return view('Admin.Customers.CustomerProfile.CustomerList',);
    }
}
