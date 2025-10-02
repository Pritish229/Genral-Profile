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

    public function manageContact($id, $type)
    {
        return view('Admin.Vendors.VendorProfile.ManageContact', ['id' => $id, 'type' => $type]);
    }

    public function getContacts($vendor_id, $type)
    {
        $contacts = VendorContact::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    public function getContact($vendor_id, $type, $contact_id)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->where('id', $contact_id)
            ->first();

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }

    public function storeContact(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $rules = [
            'contact_type' => 'required|string|max:120',
            'value'        => 'required|string|max:120',
            'label'        => 'nullable|string|max:120',
            'is_primary'   => 'nullable|boolean',
            'is_emergency' => 'nullable|boolean',
        ];

        $validated = $request->validate($rules);

        $profile_type = $request->route('type') ?? 'individual';

        // Check if this is the first contact for this profile type - make it primary
        $existingContacts = VendorContact::where('vendor_id', $vendor->id)
            ->where('profile_type', $profile_type)
            ->count();

        $data = [
            'contact_type' => $validated['contact_type'],
            'label'        => $validated['label'] ?? null,
            'value'        => $validated['value'],
            'vendor_id'    => $vendor->id,
            'tenant_id'    => $vendor->tenant_id,
            'profile_type' => $profile_type,
            'is_primary'   => $validated['is_primary'] ?? ($existingContacts === 0), // First contact becomes primary
            'is_emergency' => $validated['is_emergency'] ?? false,
        ];

        // If this contact is being set as primary, remove primary status from other contacts of same type
        if ($data['is_primary']) {
            VendorContact::where('vendor_id', $vendor->id)
                ->where('profile_type', $profile_type)
                ->update(['is_primary' => false]);
        }

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


    public function permanentContact($vendor_id, $type)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)->where('is_primary', '1')->where('profile_type', $type)->first();
        if ($contact) {
            return response()->json([
                'success' => true,
                'message' => 'Contact fetched successfully.',
                'data'    => [
                    'id' => $contact->id,
                    'contact_type' => $contact->contact_type,
                    'contact_value' => $contact->value, // Map 'value' to 'contact_value'
                    'country_code' => $contact->country_code,
                    'contact_label' => $contact->label, // Map 'label' to 'contact_label'
                    'emergency' => $contact->is_emergency ? 'Yes' : 'No', // Map 'is_emergency' to 'emergency'
                    'is_primary' => $contact->is_primary,
                    'profile_type' => $contact->profile_type,
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Vendor contact not found',
            ], 404);
        }
    }


    public function updateContact(Request $request, Vendor $vendor, $type, VendorContact $contact)
    {
        $rules = [
            'contact_type' => 'required|string|max:120',
            'value'        => 'required|string|max:120',
            'label'        => 'nullable|string|max:120',
            'is_primary'   => 'nullable|boolean',
            'is_emergency' => 'nullable|boolean',
        ];
        $validated = $request->validate($rules);

        $data = [
            'contact_type' => $validated['contact_type'],
            'label'        => $validated['label'] ?? null,
            'value'        => $validated['value'],
            'profile_type' => $type,
            'is_primary'   => $validated['is_primary'] ?? $contact->is_primary,
            'is_emergency' => $validated['is_emergency'] ?? $contact->is_emergency,
        ];

        // If this contact is being set as primary, remove primary status from other contacts of same type
        if ($data['is_primary'] && !$contact->is_primary) {
            VendorContact::where('vendor_id', $vendor->id)
                ->where('profile_type', $type)
                ->where('id', '!=', $contact->id)
                ->update(['is_primary' => false]);
        }

        $contact->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'data'    => $contact,
        ]);
    }

    public function deleteContact(Vendor $vendor, $type, VendorContact $contact)
    {
        $contact->delete();
        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }
}
