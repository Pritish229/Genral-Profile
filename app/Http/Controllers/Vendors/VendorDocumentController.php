<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorDocumentController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddDocument', ['id' => $id]);
    }
}
