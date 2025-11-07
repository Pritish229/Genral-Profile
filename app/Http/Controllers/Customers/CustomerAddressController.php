<?php

namespace App\Http\Controllers\Customers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\CustomerBusinessProfile;

class CustomerAddressController extends Controller
{
    public function index($id)
    {
        $customer = Customer::findOrFail($id);
        return view('Admin.Customers.CustomerProfile.AddAddress', ['id' => $id]);
    }

    public function manageAddress($id, $type = 'individual')
    {
        $customer = Customer::findOrFail($id);
        return view('Admin.Customers.CustomerProfile.ManageAddress', ['id' => $id, 'type' => $type]);
    }

    public function getAddresses($id)
    {
        $customer = Customer::findOrFail($id);
        $addresses = CustomerAddress::where('customer_id', $id)
            ->where('profile_type', 'individual')
            ->get();

        return response()->json(['success' => true, 'data' => $addresses]);
    }

    public function getAddress($customer_id, $address_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $address = CustomerAddress::where('customer_id', $customer_id)
            ->where('profile_type', 'individual')
            ->findOrFail($address_id);

        return response()->json(['success' => true, 'data' => $address]);
    }

    public function storeAddress(Request $request, $customer_id, $type = 'individual')
    {
        try {
            $customer = Customer::findOrFail($customer_id);

            $validated = $request->validate([
                'state'       => 'required|string|max:120',
                'district'    => 'required|string|max:120',
                'city'        => 'required|string|max:120',
                'pincode'     => 'required|digits:6',
                'line1'       => 'nullable|string|max:120',
                'line2'       => 'nullable|string|max:120',
                'landmark'    => 'nullable|string|max:150',
                'label'       => 'nullable|string|max:100',
                'longitude'   => 'nullable|string|max:50',
                'latitude'    => 'nullable|string|max:50',
                'address_type' => 'required|string|in:permanent,temporary,office',
                'is_primary'  => 'nullable|boolean',
            ]);

            if ($type === 'business') {
                $business = CustomerBusinessProfile::where('customer_id', $customer_id)->first();
                if (! $business) {
                    return response()->json(['success' => false, 'message' => 'Business profile not found.'], 404);
                }
            }

            $existingCount = CustomerAddress::where('customer_id', $customer_id)
                ->where('profile_type', $type)->count();

            if ($existingCount === 0) {
                $validated['is_primary'] = true;
            } elseif (!empty($validated['is_primary'])) {
                CustomerAddress::where('customer_id', $customer_id)
                    ->where('profile_type', $type)
                    ->update(['is_primary' => false]);
            }

            $address = CustomerAddress::create([
                'tenant_id'    => $customer->tenant_id,
                'customer_id'  => $customer_id,
                'profile_type' => $type,
                'state'        => $validated['state'],
                'district'     => $validated['district'],
                'city'         => $validated['city'],
                'pincode'      => $validated['pincode'],
                'line1'        => $validated['line1'] ?? null,
                'line2'        => $validated['line2'] ?? null,
                'landmark'     => $validated['landmark'] ?? null,
                'label'        => $validated['label'] ?? null,
                'longitude'    => $validated['longitude'] ?? null,
                'latitude'     => $validated['latitude'] ?? null,
                'address_type' => $validated['address_type'],
                'is_primary'   => $validated['is_primary'] ?? false,
            ]);

            return response()->json(['success' => true, 'message' => 'Address added successfully', 'data' => $address]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Address store DB error', [
                'customer_id' => $customer_id,
                'type' => $type,
                'error' => $e->getMessage(),
                'sql' => $e->getSql()
            ]);
            return response()->json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error in storeAddress', [
                'customer_id' => $customer_id,
                'type' => $type,
                'exception' => $e
            ]);
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function updateAddress(Request $request, $customer_id, $type, $address_id)
    {
        try {
            $customer = Customer::findOrFail($customer_id);
            if ($type !== 'individual') {
                return response()->json(['success' => false, 'message' => 'Invalid profile type for individual address update'], 400);
            }

            $validated = $request->validate([
                'state'       => 'required|string|max:120',
                'district'    => 'required|string|max:120',
                'city'        => 'required|string|max:120',
                'pincode'     => 'required|digits:6',
                'line1'       => 'nullable|string|max:120',
                'line2'       => 'nullable|string|max:120',
                'landmark'    => 'nullable|string|max:150',
                'label'       => 'nullable|string|max:100',
                'longitude'   => 'nullable|string|max:50',
                'latitude'    => 'nullable|string|max:50',
                'address_type' => 'required|string|in:permanent,other,office',
                'is_primary'  => 'nullable|boolean',
            ]);

            $address = CustomerAddress::where('customer_id', $customer_id)
                ->where('profile_type', $type)
                ->findOrFail($address_id);

            if ($validated['is_primary'] ?? false) {
                CustomerAddress::where('customer_id', $customer_id)
                    ->where('profile_type', $type)
                    ->where('id', '!=', $address_id)
                    ->update(['is_primary' => false]);
            }

            $address->update([
                'state'       => $validated['state'],
                'district'    => $validated['district'],
                'city'        => $validated['city'],
                'pincode'     => $validated['pincode'],
                'line1'       => $validated['line1'],
                'line2'       => $validated['line2'],
                'landmark'    => $validated['landmark'],
                'label'       => $validated['label'],
                'longitude'   => $validated['longitude'],
                'latitude'    => $validated['latitude'],
                'address_type' => $validated['address_type'],
                'is_primary'  => $validated['is_primary'] ?? false,
            ]);

            return response()->json(['success' => true, 'message' => 'Address updated successfully', 'data' => $address->fresh()]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in updateAddress', [
                'customer_id' => $customer_id,
                'address_id' => $address_id,
                'profile_type' => $type,
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'timestamp' => now()->toDateTimeString()
            ]);
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in updateAddress', [
                'customer_id' => $customer_id,
                'address_id' => $address_id,
                'profile_type' => $type,
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ]);
            return response()->json(['success' => false, 'message' => 'An error occurred while updating the address'], 500);
        }
    }

    public function deleteAddress($customer_id, $type, $address_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $address = CustomerAddress::where('customer_id', $customer_id)
            ->where('profile_type', $type)
            ->findOrFail($address_id);

        if ($address->is_primary) {
            return response()->json(['success' => false, 'message' => 'Cannot delete primary address. Set another as primary first.'], 403);
        }

        $address->delete();
        return response()->json(['success' => true, 'message' => 'Address deleted successfully']);
    }

    public function permanentAddress($customer_id, $type = 'individual')
    {
        $customer = Customer::findOrFail($customer_id);
        $address = CustomerAddress::where('customer_id', $customer_id)
            ->where('profile_type', $type)
            ->where('is_primary', 1)
            ->first();

        if ($address) {
            return response()->json(['success' => true, 'message' => 'Address fetched successfully', 'data' => $address]);
        }

        return response()->json(['success' => false, 'message' => 'No primary address found'], 404);
    }

    public function businessAddress($id, $business_id)
    {
        return view('Admin.Customers.CustomerProfile.BusinessAddress', ['id' => $id, 'business_id' => $business_id]);
    }

    public function getBusinessAddresses($customer_id, $business_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $business = CustomerBusinessProfile::where('customer_id', $customer_id)->findOrFail($business_id);
        $addresses = CustomerAddress::where('customer_id', $customer_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $addresses]);
    }

    public function getBusinessAddress($customer_id, $business_id, $address_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $business = CustomerBusinessProfile::where('customer_id', $customer_id)->findOrFail($business_id);
        $address = CustomerAddress::where('customer_id', $customer_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->findOrFail($address_id);

        return response()->json(['success' => true, 'data' => $address]);
    }

    public function storeBusinessAddress(Request $request, $customer_id, $business_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $business = CustomerBusinessProfile::where('customer_id', $customer_id)->findOrFail($business_id);

        $validated = $request->validate([
            'state'               => 'required|string|max:120',
            'district'            => 'required|string|max:120',
            'city'                => 'required|string|max:120',
            'pincode'             => 'required|digits:6',
            'line1'               => 'nullable|string|max:180',
            'line2'               => 'nullable|string|max:180',
            'landmark'            => 'nullable|string|max:150',
            'label'               => 'nullable|string|max:120',
            'address_type'        => 'required|in:permanent,other,hostel', 
            'address_type_name'   => 'nullable|string|max:100',
            'is_primary'          => 'nullable|boolean',
            'longitude'           => 'nullable|numeric',
            'latitude'            => 'nullable|numeric',
            'contact_person_type' => 'nullable|string|max:200',
            'contact_person_name' => 'nullable|string|max:200',
            'department'          => 'nullable|string|max:100',
            'designation'         => 'nullable|string|max:100',
        ]);

        // Unset primary for same type if needed
        if ($validated['is_primary'] ?? false) {
            CustomerAddress::where('customer_id', $customer_id)
                ->where('business_id', $business_id)
                ->where('profile_type', 'business')
                ->where('address_type', $validated['address_type'])
                ->update(['is_primary' => false]);
        }

        $address = CustomerAddress::create(array_merge($validated, [
            'customer_id'   => $customer_id,
            'business_id'   => $business_id,
            'tenant_id'     => $customer->tenant_id,
            'profile_type'  => 'business',
            'business_name' => $business->business_name,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Business address created successfully',
            'data'    => $address
        ]);
    }

    public function updateBusinessAddress(Request $request, $customer_id, $business_id, $address_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $business = CustomerBusinessProfile::where('customer_id', $customer_id)->findOrFail($business_id);

        $validated = $request->validate([
            'state'               => 'required|string|max:120',
            'district'            => 'required|string|max:120',
            'city'                => 'required|string|max:120',
            'pincode'             => 'required|digits:6',
            'line1'               => 'nullable|string|max:180',
            'line2'               => 'nullable|string|max:180',
            'landmark'            => 'nullable|string|max:150',
            'label'               => 'nullable|string|max:120',
            'address_type'        => 'required|in:permanent,other,hostel',
            'address_type_name'   => 'nullable|string|max:100',
            'is_primary'          => 'nullable|boolean',
            'longitude'           => 'nullable|numeric',
            'latitude'            => 'nullable|numeric',
            'contact_person_type' => 'nullable|string|max:200',
            'contact_person_name' => 'nullable|string|max:200',
            'department'          => 'nullable|string|max:100',
            'designation'         => 'nullable|string|max:100',
        ]);

        $address = CustomerAddress::where('customer_id', $customer_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->findOrFail($address_id);

        if ($validated['is_primary'] ?? false) {
            CustomerAddress::where('customer_id', $customer_id)
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

    public function deleteBusinessAddress($customer_id, $business_id, $address_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $business = CustomerBusinessProfile::where('customer_id', $customer_id)->findOrFail($business_id);
        $address = CustomerAddress::where('customer_id', $customer_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->findOrFail($address_id);

        if ($address->is_primary) {
            return response()->json(['success' => false, 'message' => 'Cannot delete primary address'], 403);
        }

        $address->delete();
        return response()->json(['success' => true, 'message' => 'Address deleted successfully']);
    }

    public function permanentBusinessAddress($id, $business_id)
    {
        try {
            $address = CustomerAddress::where('business_id', $business_id)
                ->where('customer_id', $id)
                ->where('profile_type', 'business')
                ->where('is_primary', 1)
                ->first();

            if (!$address) {
                return response()->json(['success' => false, 'message' => 'No address found for this business.'], 404);
            }

            return response()->json(['success' => true, 'data' => $address]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
