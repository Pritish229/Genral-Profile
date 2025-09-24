<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;

class VendorBusinessProfileController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessInfo', ['id' => $id]);
    }


    public function updateBusinessInfo(Request $request, $id)
    {
        // Find the vendor
        $vendor = Vendor::findOrFail($id);
        $data = $request->all();

        $data['vendor_id'] = $vendor->id;
        $data['tenant_id'] = $vendor->tenant_id;
        $data['credit_limit'] = $request->credit_limit ?? 0;

        $profile = VendorBusinessProfile::updateOrCreate(
            ['vendor_id' => $vendor->id], 
            $data 
        );

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }
}
