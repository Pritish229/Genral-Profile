<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorBankController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddBankinfo', ['id' => $id]);
    }
}
