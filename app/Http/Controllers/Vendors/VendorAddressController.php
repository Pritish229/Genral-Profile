<?php

namespace App\Http\Controllers\Vendors;

use Illuminate\Http\Request;
use App\Models\Vendor\Vendor;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Vendor\VendorAddress;
use App\Models\Vendor\VendorBusinessProfile;

class VendorAddressController extends Controller
{
    // Individual Address Methods

    public function index($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('Admin.Vendors.VendorProfile.AddAddress', ['id' => $id]);
    }

    public function manageAddress($id, $type = 'individual')
    {
        $vendor = Vendor::findOrFail($id);
        return view('Admin.Vendors.VendorProfile.ManageAddress', ['id' => $id, 'type' => $type]);
    }

    public function getAddresses($id)
    {
        $vendor = Vendor::findOrFail($id);
        $addresses = VendorAddress::where('vendor_id', $id)
            ->where('profile_type', 'individual')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    public function getAddress($vendor_id, $address_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('profile_type', 'individual')
            ->findOrFail($address_id);

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }

    public function storeAddress(Request $request, $vendor_id, $type = 'individual')
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $validated = $request->validate([
            'state' => 'required|string|max:120',
            'district' => 'required|string|max:120',
            'city' => 'required|string|max:120',
            'pincode' => 'required|digits:6',
            'line1' => 'nullable|string|max:120',
            'line2' => 'nullable|string|max:120',
            'landmark' => 'nullable|string|max:150',
            'label' => 'nullable|string|max:100',
            'longitude' => 'nullable|string|max:50',
            'latitude' => 'nullable|string|max:50',
            'address_type' => 'required|string|in:permanent,temporary,office',
            'is_primary' => 'nullable|boolean',
        ]);

        // ✅ Check for business profile (if type is business)
        if ($type === 'business') {
            $business = VendorBusinessProfile::where('vendor_id', $vendor_id)->first();
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found. Please create business profile first.'
                ], 404);
            }
        }

        // ✅ Count existing addresses for this vendor and type
        $existingCount = VendorAddress::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->count();

        // ✅ If this is the first address, automatically make it primary
        if ($existingCount === 0) {
            $validated['is_primary'] = true;
        } elseif ($validated['is_primary'] ?? false) {
            // If explicitly marked as primary, unset others
            VendorAddress::where('vendor_id', $vendor_id)
                ->where('profile_type', $type)
                ->update(['is_primary' => false]);
        }

        // ✅ Create the address
        $address = VendorAddress::create([
            'tenant_id' => $vendor->tenant_id,
            'vendor_id' => $vendor_id,
            'profile_type' => $type,
            'state' => $validated['state'],
            'district' => $validated['district'],
            'city' => $validated['city'],
            'pincode' => $validated['pincode'],
            'line1' => $validated['line1'] ?? null,
            'line2' => $validated['line2'] ?? null,
            'landmark' => $validated['landmark'] ?? null,
            'label' => $validated['label'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'address_type' => $validated['address_type'],
            'is_primary' => $validated['is_primary'] ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'data' => $address
        ]);
    }


    public function updateAddress(Request $request, $vendor_id, $type, $address_id)
    {
        try {
            $vendor = Vendor::findOrFail($vendor_id);

            if ($type !== 'individual') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid profile type for individual address update'
                ], 400);
            }

            // Validate request data
            $validated = $request->validate([
                'state' => 'required|string|max:120',
                'district' => 'required|string|max:120',
                'city' => 'required|string|max:120',
                'pincode' => 'required|digits:6',
                'line1' => 'nullable|string|max:120',
                'line2' => 'nullable|string|max:120',
                'landmark' => 'nullable|string|max:150',
                'label' => 'nullable|string|max:100',
                'longitude' => 'nullable|string|max:50',
                'latitude' => 'nullable|string|max:50',
                'address_type' => 'required|string|in:permanent,temporary,office',
                'is_primary' => 'nullable|boolean',
            ]);

            // Find the address
            $address = VendorAddress::where('vendor_id', $vendor_id)
                ->where('profile_type', $type)
                ->findOrFail($address_id);

            // If setting as primary, reset other addresses
            if ($validated['is_primary'] ?? false) {
                VendorAddress::where('vendor_id', $vendor_id)
                    ->where('profile_type', $type)
                    ->where('id', '!=', $address_id)
                    ->update(['is_primary' => false]);
            }

            // Update the address
            $address->update([
                'state' => $validated['state'],
                'district' => $validated['district'],
                'city' => $validated['city'],
                'pincode' => $validated['pincode'],
                'line1' => $validated['line1'],
                'line2' => $validated['line2'],
                'landmark' => $validated['landmark'],
                'label' => $validated['label'],
                'longitude' => $validated['longitude'],
                'latitude' => $validated['latitude'],
                'address_type' => $validated['address_type'],
                'is_primary' => $validated['is_primary'] ?? false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'data' => $address->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in updateAddress', [
                'vendor_id' => $vendor_id,
                'address_id' => $address_id,
                'profile_type' => $type,
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in updateAddress', [
                'vendor_id' => $vendor_id,
                'address_id' => $address_id,
                'profile_type' => $type,
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the address'
            ], 500);
        }
    }

    public function deleteAddress($vendor_id, $type, $address_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->findOrFail($address_id);

        if ($address->is_primary) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete primary address. Please set another address as primary first.'
            ], 403);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    public function permanentAddress($vendor_id, $type = 'individual')
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->where('is_primary', 1)
            ->first();

        if ($address) {
            return response()->json([
                'success' => true,
                'message' => 'Address fetched successfully',
                'data' => $address
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No primary address found'
        ], 404);
    }

    // Business Address Methods

    public function businessAddress($id, $business_id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessAddress', ['id' => $id, 'business_id' => $business_id]);
    }

    public function getBusinessAddresses($vendor_id, $business_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)->findOrFail($business_id);
        $addresses = VendorAddress::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
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
        $vendor = Vendor::findOrFail($vendor_id);
        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)->findOrFail($business_id);
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->findOrFail($address_id);

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }

    public function storeBusinessAddress(Request $request, $vendor_id, $business_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)->findOrFail($business_id);

        $validated = $request->validate([
            'state'               => 'required|string|max:120',
            'district'            => 'required|string|max:120',
            'city'                => 'required|string|max:120',
            'pincode'             => 'required|digits:6',
            'line1'               => 'nullable|string|max:180',
            'line2'               => 'nullable|string|max:180',
            'landmark'            => 'nullable|string|max:150',
            'label'               => 'nullable|string|max:120',
            'address_type'        => 'required|in:permanent,office,billing,shipping,other',
            'address_type_name'   => 'nullable|string|max:120',
            'is_primary'          => 'nullable|boolean',
            'longitude'           => 'nullable|numeric',
            'latitude'            => 'nullable|numeric',
        ]);

        if ($validated['is_primary'] ?? false) {
            VendorAddress::where('vendor_id', $vendor_id)
                ->where('business_id', $business_id)
                ->where('profile_type', 'business')
                ->where('address_type', $validated['address_type'])
                ->update(['is_primary' => false]);
        }

        $address = VendorAddress::create(array_merge($validated, [
            'vendor_id'     => $vendor_id,
            'business_id'   => $business_id,
            'tenant_id'     => $vendor->tenant_id,
            'profile_type'  => 'business',
            'business_name' => $business->business_name,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Business address created successfully',
            'data'    => $address
        ]);
    }

    public function updateBusinessAddress(Request $request, $vendor_id, $business_id, $address_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)->findOrFail($business_id);

        $validated = $request->validate([
            'state'               => 'required|string|max:120',
            'district'            => 'required|string|max:120',
            'city'                => 'required|string|max:120',
            'pincode'             => 'required|digits:6',
            'line1'               => 'nullable|string|max:180',
            'line2'               => 'nullable|string|max:180',
            'landmark'            => 'nullable|string|max:150',
            'label'               => 'nullable|string|max:120',
            'address_type'        => 'required|in:permanent,office,billing,shipping,other',
            'address_type_name'   => 'nullable|string|max:120',
            'is_primary'          => 'nullable|boolean',
            'longitude'           => 'nullable|numeric',
            'latitude'            => 'nullable|numeric',
        ]);

        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->findOrFail($address_id);

        if ($validated['is_primary'] ?? false) {
            VendorAddress::where('vendor_id', $vendor_id)
                ->where('business_id', $business_id)
                ->where('profile_type', 'business')
                ->where('address_type', $validated['address_type'])
                ->where('id', '!=', $address_id)
                ->update(['is_primary' => false]);
        }

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business address updated successfully',
            'data'    => $address->fresh()
        ]);
    }


    public function deleteBusinessAddress($vendor_id, $business_id, $address_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)->findOrFail($business_id);
        $address = VendorAddress::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->findOrFail($address_id);

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

    public function permanentBusinessAddress($id, $business_id)
    {
        try {
            $address = VendorAddress::where('business_id', $business_id)
                ->where('vendor_id', $id)
                ->where('profile_type', 'business')
                ->where('is_primary', 1)
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'No address found for this business.',
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $address,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
