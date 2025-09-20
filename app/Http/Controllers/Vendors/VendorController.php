<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function create()
    {
        return view('Admin.Vendors.VendorProfile.AddVendor');
    }
    public function vendorlist()
    {
        return view('Admin.Vendors.VendorProfile.VendorsList',);
    }
}
