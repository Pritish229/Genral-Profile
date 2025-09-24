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
            'line_1'     => 'nullable|string|max:120',
            'line_2'     => 'nullable|string|max:120',
            'landmark'   => 'nullable|string|max:150',
            'label'      => 'nullable|string|max:100',
            'longitude'  => 'nullable|string|max:50',
            'latitude'   => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);

        $vendor = Vendor::findOrFail($vendor_id);

        $address = VendorAddress::create(array_merge($validated, [
            'vendor_id' => $vendor->id,
            'tenant_id'  => $vendor->tenant_id,
            'is_primary'  => '1',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully',
            'data'    => $address
        ], 201);
    }

    public function permanentAddress($vendor_id)
    {
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('is_primary', 1)
            ->first();

        if ($address) {
            return response()->json([
                'success' => true,
                'message' => 'Address fetched successfully',
                'data'    => $address
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No primary address found'
        ], 404);
    }
}
