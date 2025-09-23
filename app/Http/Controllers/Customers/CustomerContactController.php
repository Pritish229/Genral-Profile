<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerContactController extends Controller
{
    public function index($id)
    {
        return view('Admin.Customers.CustomerProfile.AddContact', ['id' => $id]);
    }
}
