<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeAddressController extends Controller
{
    public function index($id)
    {
        return view('Admin.Employees.EmployeeProfile.AddAddress', ['id' => $id]);
    }
}
