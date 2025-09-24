<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VendorIndividualProfile;

class VendorProfileController extends Controller
{
    public function Details($id){
        $data = VendorIndividualProfile::where('vendor_id',$id)->first();
        $vendors = Vendor::where('id',$id)->first();

        return response()->json([
            'success' => true,
            'data' => $data,
            'primary_details' => $vendors
        ], 200);
    }
}
