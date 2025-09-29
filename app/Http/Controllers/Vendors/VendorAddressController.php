<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorAddress;
use App\Http\Controllers\Controller;

class VendorAddressController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddAddress', ['id' => $id]);
    }

    public function storeAddress(Request $request, $vendor_id)
    {
        $validated = $request->validate([
            'state'      => 'required|string|max:120',
            'district'   => 'required|string|max:120',
            'city'       => 'required|string|max:120',
            'pincode'    => 'required|digits:6',
            'line1'      => 'nullable|string|max:120',
            'line2'      => 'nullable|string|max:120',
            'landmark'   => 'nullable|string|max:150',
            'label'      => 'nullable|string|max:100',
            'longitude'  => 'nullable|string|max:50',
            'latitude'   => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);

       
        
        $vendor = Vendor::find($vendor_id);
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $address = VendorAddress::create(array_merge($validated, [
            'tenant_id' => '1',
            'is_primary' => 1,
            'profile_type' => $vendor->type,
            'vendor_id' => $vendor->id,
            'state' => $request->state,
            'district' => $request->district,
            'city' => $request->city,
            'pincode' => $request->pincode,
            'line1' => $request->line1,
            'line2' => $request->line2,
            'landmark' => $request->landmark,
            'label' => $request->label,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude, 
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Address Added successfully',
            'data'    => $address
        ], 200);
    }

    public function permanentAddress($vendor_id)
    {
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('is_primary', 1)
            ->first();

        if ($address) {
            return response()->json([
                'success' => true,
                'message' => 'Address Fetched Successfully',
                'data'    => $address
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No primary address found'
        ], 404);
    }
}
