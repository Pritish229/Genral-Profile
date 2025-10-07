<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorAddress;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;

class VendorAddressController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddAddress', ['id' => $id]);
    }
    public function businessAddress($id, $business_id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessAddress', ['id' => $id, 'business_id' => $business_id]);
    }

    
    public function manageAddress($id, $type)
    {
        return view('Admin.Vendors.VendorProfile.ManageAddress', ['id' => $id, 'type' => $type]);
    }

    public function getAddresses($vendor_id, $type)
    {
        $addresses = VendorAddress::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    public function getAddress($vendor_id, $business_id, $address_id)
    {
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->find($address_id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }

    public function storeAddress(Request $request, $vendor_id, $type)
    {
        $validated = $request->validate([
            'state'        => 'required|string|max:120',
            'district'     => 'required|string|max:120',
            'city'         => 'required|string|max:120',
            'pincode'      => 'required|digits:6',
            'line1'        => 'nullable|string|max:120',
            'line2'        => 'nullable|string|max:120',
            'landmark'     => 'nullable|string|max:150',
            'label'        => 'nullable|string|max:100',
            'longitude'    => 'nullable|string|max:50',
            'latitude'     => 'nullable|string|max:50',
            'address_type' => 'nullable|string|in:permanent,temporary,office,billing,shipping',
            'is_primary'   => 'nullable|boolean',
        ]);

        $vendor = Vendor::find($vendor_id);
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        // Check if business profile exists for business type
        if ($type === 'business') {
            $business = VendorBusinessProfile::where('vendor_id', $vendor_id)->first();
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found. Please create business profile first.'
                ], 404);
            }
        }

        // If setting as primary, remove primary status from other addresses of same type
        if ($validated['is_primary'] ?? false) {
            VendorAddress::where('vendor_id', $vendor_id)
                ->where('profile_type', $type)
                ->update(['is_primary' => false]);
        }

        $address = VendorAddress::create([
            'tenant_id'    => 1,
            'vendor_id'    => $vendor_id,
            'profile_type' => $type,
            'state'        => $validated['state'],
            'district'     => $validated['district'],
            'city'         => $validated['city'],
            'pincode'      => $validated['pincode'],
            'line1'        => $validated['line1'],
            'line2'        => $validated['line2'],
            'landmark'     => $validated['landmark'],
            'label'        => $validated['label'],
            'longitude'    => $validated['longitude'],
            'latitude'     => $validated['latitude'],
            'address_type' => $validated['address_type'] ?? 'permanent',
            'is_primary'   => $validated['is_primary'] ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'data'    => $address
        ]);
    }

    public function updateAddress(Request $request, $vendor_id, $type, $address_id)
    {
        $validated = $request->validate([
            'state'        => 'required|string|max:120',
            'district'     => 'required|string|max:120',
            'city'         => 'required|string|max:120',
            'pincode'      => 'required|digits:6',
            'line1'        => 'nullable|string|max:120',
            'line2'        => 'nullable|string|max:120',
            'landmark'     => 'nullable|string|max:150',
            'label'        => 'nullable|string|max:100',
            'longitude'    => 'nullable|string|max:50',
            'latitude'     => 'nullable|string|max:50',
            'address_type' => 'nullable|string|in:permanent,temporary,office,billing,shipping',
            'is_primary'   => 'nullable|boolean',
        ]);

        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->where('id', $address_id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        // If setting as primary, remove primary status from other addresses of same type
        if ($validated['is_primary'] ?? false) {
            VendorAddress::where('vendor_id', $vendor_id)
                ->where('profile_type', $type)
                ->where('id', '!=', $address_id)
                ->update(['is_primary' => false]);
        }

        $address->update([
            'state'        => $validated['state'],
            'district'     => $validated['district'],
            'city'         => $validated['city'],
            'pincode'      => $validated['pincode'],
            'line1'        => $validated['line1'],
            'line2'        => $validated['line2'],
            'landmark'     => $validated['landmark'],
            'label'        => $validated['label'],
            'longitude'    => $validated['longitude'],
            'latitude'     => $validated['latitude'],
            'address_type' => $validated['address_type'] ?? $address->address_type,
            'is_primary'   => $validated['is_primary'] ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data'    => $address->fresh()
        ]);
    }

    public function deleteAddress($vendor_id, $type, $address_id)
    {
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->where('id', $address_id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        // Prevent deletion of primary address
        if ($address->is_primary) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete primary address. Please set another address as primary first.'
            ], 400);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    public function permanentAddress($vendor_id, $type)
    {
        $address = VendorAddress::where('vendor_id', $vendor_id)->where('profile_type', $type)
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

    public function permanentBusinessAddress($vendor_id, $business_id)
    {
        $address = VendorAddress::where('vendor_id', $vendor_id)->where('business_id', $business_id)->where('is_primary', '1')->first();
        if ($address) {
            return response()->json([
                'success' => true,
                'message' => 'Address fetched successfully',
                'data'    => $address
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Vendor address not found',
            ], 404);
        }
    }

    public function listBusinessAddresses($vendor_id, $business_id)
    {
        $addresses = VendorAddress::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    public function getBusinessAddress($vendor_id, $business_id, $address_id)
    {
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->find($address_id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }

    public function storeBusinessAddress(Request $request, $vendor_id, $business_id)
    {
        $validated = $request->validate([
            'state' => 'nullable|string|max:120',
            'district' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'pincode' => 'nullable|string|max:10',
            'line1' => 'nullable|string|max:255',
            'line2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'label' => 'nullable|string|max:120',
            'address_type' => 'required|string|in:permanent,office,billing,shipping',
            'is_primary' => 'nullable|boolean',
            'longitude' => 'nullable|string|max:50',
            'latitude' => 'nullable|string|max:50'
        ]);

        $vendor = Vendor::findOrFail($vendor_id);

        $address = VendorAddress::create(array_merge($validated, [
            'vendor_id' => $vendor_id,
            'business_id' => $business_id,
            'tenant_id' => $vendor->tenant_id,
        ]));

        if (!empty($validated['is_primary'])) {
            VendorAddress::where('vendor_id', $vendor_id)
                ->where('business_id', $business_id)
                ->where('address_type', $validated['address_type'])
                ->where('id', '!=', $address->id)
                ->update(['is_primary' => false]);

            $address->is_primary = true;
            $address->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Business address created successfully',
            'data' => $address
        ]);
    }

    public function updateBusinessAddress(Request $request, $vendor_id, $business_id, $address_id)
    {
        $validated = $request->validate([
            'state' => 'nullable|string|max:120',
            'district' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'pincode' => 'nullable|string|max:10',
            'line1' => 'nullable|string|max:255',
            'line2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'label' => 'nullable|string|max:120',
            'address_type' => 'required|string|in:permanent,office,billing,shipping',
            'is_primary' => 'nullable|boolean',
            'longitude' => 'nullable|string|max:50',
            'latitude' => 'nullable|string|max:50'
        ]);

        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->find($address_id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $address->update($validated);

        if (!empty($validated['is_primary'])) {
            VendorAddress::where('vendor_id', $vendor_id)
                ->where('business_id', $business_id)
                ->where('address_type', $validated['address_type'])
                ->where('id', '!=', $address->id)
                ->update(['is_primary' => false]);

            $address->is_primary = true;
            $address->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Business address updated successfully',
            'data' => $address
        ]);
    }

    public function deleteBusinessAddress($vendor_id, $business_id, $address_id)
    {
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->find($address_id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        if ($address->is_primary) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete primary address'
            ], 403);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }
}
