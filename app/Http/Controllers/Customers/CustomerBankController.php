<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerBankController extends Controller
{
    public function index($id)
    {
        return view('Admin.Customers.CustomerProfile.AddBankinfo', ['id' => $id]);
    }
}
