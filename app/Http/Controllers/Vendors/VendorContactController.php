<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorContact;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;

class VendorContactController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddContact', ['id' => $id]);
    }

    public function storeContact(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $rules = [
            'contact_type' => 'required|string|max:120',
            'value'        => 'required|string|max:120',
            'label'        => 'nullable|string|max:120',
        ];

        $validated = $request->validate($rules);

        $data = [
            'contact_type' => $validated['contact_type'],
            'label'        => $validated['label'] ?? null,
            'value'        => $validated['value'],
            'vendor_id'    => $vendor->id,
            'tenant_id'    => $vendor->tenant_id,
        ];

        if ($vendor->type === 'business') {
            $business = VendorBusinessProfile::where('vendor_id', $vendor->id)->first();

            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found for this vendor.'
                ], 422);
            }

            $data['business_id']   = $business->id;
            $data['business_name'] = $business->trade_name;
        }

        $contact = VendorContact::create($data);

        return response()->json([
            'success' => true,
            'data'    => $contact
        ], 201);
    }


    public function permanentContact($vendor_id)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)->where('is_primary', '1')->first();
        if ($contact) {
            return response()->json([
                'success' => true,
                'message' => 'Contact Fatch successfully.',
                'data'    => $contact
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'employee contact not found',
            ], 404);
        }
    }
}
