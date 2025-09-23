<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerDocumentController extends Controller
{
    public function index($id)
    {
        return view('Admin.Customers.CustomerProfile.AddDocument', ['id' => $id]);
    }
}
