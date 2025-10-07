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

    public function BusinessContact($id, $business_id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessContact', ['id' => $id, 'business_id' => $business_id]);
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

    public function getBusinessContacts($vendor_id, $business_id)
    {
        $contacts = VendorContact::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
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
            'is_primary'   => $validated['is_primary'] ?? ($existingContacts === 0),
            'is_emergency' => $validated['is_emergency'] ?? false,
        ];

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

        if ($data['is_primary']) {
            $this->syncPrimaryContact($contact);
        }

        return response()->json([
            'success' => true,
            'data'    => $contact
        ], 201);
    }

    public function permanentContact($vendor_id, $type)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('is_primary', '1')
            ->where('profile_type', $type)
            ->first();

        if ($contact) {
            return response()->json([
                'success' => true,
                'message' => 'Contact fetched successfully.',
                'data'    => [
                    'id' => $contact->id,
                    'contact_type' => $contact->contact_type,
                    'contact_value' => $contact->value,
                    'country_code' => $contact->country_code,
                    'contact_label' => $contact->label,
                    'emergency' => $contact->is_emergency ? 'Yes' : 'No',
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

        if ($data['is_primary'] && !$contact->is_primary) {
            VendorContact::where('vendor_id', $vendor->id)
                ->where('profile_type', $type)
                ->where('id', '!=', $contact->id)
                ->update(['is_primary' => false]);
        }

        $contact->update($data);

        if ($data['is_primary']) {
            $this->syncPrimaryContact($contact);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'data'    => $contact,
        ]);
    }

    public function deleteContact(Vendor $vendor, $type, VendorContact $contact)
    {
        $wasPrimary = $contact->is_primary;
        $contact->delete();

        if ($wasPrimary) {
            $this->assignNewPrimaryContactIndividual($vendor->id, $type);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }

    // ================= BUSINESS CONTACTS ================= //

    public function addBusinessContact(Request $request, $vendor_id, $business_id)
    {
        $validated = $request->validate([
            'contact_type' => 'required|string|max:120',
            'value' => 'required|string|max:120',
            'country_code' => 'nullable|string|max:2',
            'label' => 'nullable|string|max:120',
            'is_primary' => 'nullable|boolean',
            'is_emergency' => 'nullable|boolean',
        ]);

        $vendor = Vendor::findOrFail($vendor_id);
        $business = VendorBusinessProfile::findOrFail($business_id);

        $type = strtolower($validated['contact_type']); // phone/email

        $isPrimary = $validated['is_primary'] ?? false; // No automatic primary

        if ($isPrimary) {
            // Remove other primary of same type
            VendorContact::where('vendor_id', $vendor_id)
                ->where('business_id', $business_id)
                ->where('contact_type', $type)
                ->update(['is_primary' => false]);
        }

        $contact = VendorContact::create([
            'contact_type' => $validated['contact_type'],
            'value' => $validated['value'],
            'vendor_id' => $vendor_id,
            'business_id' => $business_id,
            'country_code' => $validated['country_code'],
            'label' => $validated['label'] ?? null,
            'is_primary' => $isPrimary,
            'is_emergency' => $validated['is_emergency'] ?? false,
            'profile_type' => 'business',
            'tenant_id' => $vendor->tenant_id,
            'business_name' => $business->trade_name
        ]);

        // If primary, update VendorBusinessProfile field
        if ($isPrimary) {
            if ($type === 'phone') {
                $business->primary_contact_phone = $contact->value;
            } elseif ($type === 'email') {
                $business->primary_contact_email = $contact->value;
            }
            $business->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Business contact added successfully',
            'data' => $contact
        ], 201);
    }

    public function updateBusinessContact(Request $request, $vendor_id, $business_id, $contact_id)
    {
        $validated = $request->validate([
            'contact_type' => 'required|string|max:120',
            'value' => 'required|string|max:120',
            'label' => 'nullable|string|max:120',
            'country_code' => 'nullable|string|max:2',
            'is_primary' => 'nullable|boolean',
            'is_emergency' => 'nullable|boolean',
        ]);

        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('id', $contact_id)
            ->firstOrFail();

        $type = strtolower($validated['contact_type']);

        $isPrimary = $validated['is_primary'] ?? $contact->is_primary;

        if ($isPrimary && !$contact->is_primary) {
            // Remove other primary of same type
            VendorContact::where('vendor_id', $vendor_id)
                ->where('business_id', $business_id)
                ->where('contact_type', $type)
                ->update(['is_primary' => false]);
        }

        $contact->update([
            'contact_type' => $validated['contact_type'],
            'value' => $validated['value'],
            'country_code' => $validated['country_code'] ?? $contact->country_code,
            'label' => $validated['label'] ?? $contact->label,
            'is_emergency' => $validated['is_emergency'] ?? $contact->is_emergency,
            'is_primary' => $isPrimary
        ]);

        // If primary, update VendorBusinessProfile
        if ($isPrimary) {
            $business = VendorBusinessProfile::find($business_id);
            if ($type === 'phone') {
                $business->primary_contact_phone = $contact->value;
            } elseif ($type === 'email') {
                $business->primary_contact_email = $contact->value;
            }
            $business->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Business contact updated successfully',
            'data' => $contact
        ]);
    }


    public function deleteBusinessContact($vendor_id, $business_id, $contact_id)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('id', $contact_id)
            ->firstOrFail();

        $wasPrimary = $contact->is_primary;
        $contact->delete();

        if ($wasPrimary) {
            $this->assignNewPrimaryContact($vendor_id, $business_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Business contact deleted successfully'
        ]);
    }

    public function getBusinessContact($id, $business_id, $contact_id)
    {
        $contact = VendorContact::where('vendor_id', $id)
            ->where('business_id', $business_id)
            ->where('id', $contact_id)
            ->first();

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Business contact not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }

    public function permanentBusinessContact($vendor_id, $business_id)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('is_primary', '1')
            ->first();

        if ($contact) {
            return response()->json(['success' => true, 'message' => 'Contact fetched successfully.', 'data' => $contact], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Vendor contact not found'], 404);
        }
    }

    // ================= HELPER METHODS ================= //

    private function assignNewPrimaryContact($vendor_id, $business_id)
    {
        $newPrimary = VendorContact::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->orderBy('created_at', 'asc')
            ->first();

        if ($newPrimary) {
            $newPrimary->is_primary = true;
            $newPrimary->save();
            $this->syncPrimaryContact($newPrimary);
        }
    }

    private function assignNewPrimaryContactIndividual($vendor_id, $profile_type)
    {
        $newPrimary = VendorContact::where('vendor_id', $vendor_id)
            ->where('profile_type', $profile_type)
            ->orderBy('created_at', 'asc')
            ->first();

        if ($newPrimary) {
            $newPrimary->is_primary = true;
            $newPrimary->save();
            $this->syncPrimaryContact($newPrimary);
        }
    }

    private function syncPrimaryContact(VendorContact $contact)
    {
        if ($contact->profile_type === 'business' && $contact->business_id) {
            $business = VendorBusinessProfile::find($contact->business_id);
            if (!$business) return;

            if (strtolower($contact->contact_type) === 'email') {
                $business->primary_contact_email = $contact->value;
            } elseif (strtolower($contact->contact_type) === 'phone') {
                $business->primary_contact_phone = $contact->value;
            }

            $business->save();
        } else {
            $vendor = Vendor::find($contact->vendor_id);
            if (!$vendor) return;

            if (strtolower($contact->contact_type) === 'email') {
                $vendor->primary_contact_email = $contact->value;
            } elseif (strtolower($contact->contact_type) === 'phone') {
                $vendor->primary_contact_phone = $contact->value;
            }

            $vendor->save();
        }
    }
}
