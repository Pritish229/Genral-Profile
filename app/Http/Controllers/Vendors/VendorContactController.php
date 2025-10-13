<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorContact;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;

class VendorContactController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.ManageContact', ['id' => $id]);
    }

    public function businessContact($id, $business_id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessContact', ['id' => $id, 'business_id' => $business_id]);
    }

    public function manageContact($id)
    {
        return view('Admin.Vendors.VendorProfile.ManageContact', ['id' => $id]);
    }

    public function getContacts($vendor_id)
    {
        $contacts = VendorContact::where('vendor_id', $vendor_id)
            ->where('profile_type', 'individual')
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
            ->find($contact_id);

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
        $validated = $request->validate([
            'contact_type' => 'required|string|in:phone,email,mobile,whatsapp,telegram,skype,other',
            'value' => 'required|string|max:255',
            'country_code' => 'nullable|string|max:10',
            'label' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
            'is_emergency' => 'nullable|boolean',
        ]);

        $vendor = Vendor::find($vendor_id);
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        // If setting as primary for phone or email, unset other primary contacts of the same contact_type
        if (($validated['is_primary'] ?? false) && in_array($validated['contact_type'], ['phone', 'email'])) {
            VendorContact::where('vendor_id', $vendor_id)
                ->where('profile_type', 'individual')
                ->where('contact_type', $validated['contact_type'])
                ->update(['is_primary' => false]);
        }

        $contact = VendorContact::create([
            'tenant_id' => $vendor->tenant_id,
            'vendor_id' => $vendor_id,
            'profile_type' => 'individual',
            'contact_type' => $validated['contact_type'],
            'value' => $validated['value'],
            'country_code' => $validated['country_code'],
            'label' => $validated['label'],
            'is_primary' => $validated['is_primary'] ?? false,
            'is_emergency' => $validated['is_emergency'] ?? false,
        ]);

        // Sync primary_email or primary_phone in Vendor model
        if ($contact->is_primary && in_array($contact->contact_type, ['phone', 'email'])) {
            $updateData = [];
            if ($contact->contact_type === 'email') {
                $updateData['primary_email'] = $contact->value;
            } elseif ($contact->contact_type === 'phone') {
                $updateData['primary_phone'] = $contact->country_code ? $contact->country_code . $contact->value : $contact->value;
            }
            $vendor->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact added successfully',
            'data' => $contact
        ]);
    }

    public function updateContact(Request $request, $vendor_id, $type, $contact_id)
    {
        $validated = $request->validate([
            'contact_type' => 'required|string|in:phone,email,mobile,whatsapp,telegram,skype,other',
            'value' => 'required|string|max:255',
            'country_code' => 'nullable|string|max:10',
            'label' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
            'is_emergency' => 'nullable|boolean',
        ]);

        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->find($contact_id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        $vendor = Vendor::find($vendor_id);
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        // If setting as primary for phone or email, unset other primary contacts of the same contact_type
        if (($validated['is_primary'] ?? false) && in_array($validated['contact_type'], ['phone', 'email'])) {
            VendorContact::where('vendor_id', $vendor_id)
                ->where('profile_type', $type)
                ->where('contact_type', $validated['contact_type'])
                ->where('id', '!=', $contact_id)
                ->update(['is_primary' => false]);
        }

        // Check if the contact was previously primary and is being unset
        $wasPrimary = $contact->is_primary;
        $isPrimaryNow = $validated['is_primary'] ?? false;

        $contact->update([
            'contact_type' => $validated['contact_type'],
            'value' => $validated['value'],
            'country_code' => $validated['country_code'],
            'label' => $validated['label'],
            'is_primary' => $isPrimaryNow,
            'is_emergency' => $validated['is_emergency'] ?? false,
        ]);

        // Sync primary_email or primary_phone in Vendor model
        if (in_array($validated['contact_type'], ['phone', 'email'])) {
            $updateData = [];
            if ($isPrimaryNow) {
                // Set primary_email or primary_phone
                if ($validated['contact_type'] === 'email') {
                    $updateData['primary_email'] = $validated['value'];
                } elseif ($validated['contact_type'] === 'phone') {
                    $updateData['primary_phone'] = $validated['country_code'] ? $validated['country_code'] . $validated['value'] : $validated['value'];
                }
            } elseif ($wasPrimary && !$isPrimaryNow) {
                // Clear primary_email or primary_phone if no other primary contact exists for this type
                $hasOtherPrimary = VendorContact::where('vendor_id', $vendor_id)
                    ->where('profile_type', $type)
                    ->where('contact_type', $validated['contact_type'])
                    ->where('id', '!=', $contact_id)
                    ->where('is_primary', true)
                    ->exists();

                if (!$hasOtherPrimary) {
                    if ($validated['contact_type'] === 'email') {
                        $updateData['primary_email'] = null;
                    } elseif ($validated['contact_type'] === 'phone') {
                        $updateData['primary_phone'] = null;
                    }
                }
            }
            if (!empty($updateData)) {
                $vendor->update($updateData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'data' => $contact->fresh()
        ]);
    }

    public function deleteContact($vendor_id, $type, $contact_id)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->find($contact_id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        if ($contact->is_primary) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete primary contact. Please set another contact as primary first.'
            ], 400);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }

    public function permanentContact($vendor_id)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('profile_type', 'individual')
            ->where('is_primary', 1)
            ->first();

        if ($contact) {
            return response()->json([
                'success' => true,
                'message' => 'Contact fetched successfully',
                'data' => $contact
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No primary contact found'
        ], 404);
    }

    public function permanentBusinessContact($vendor_id, $business_id)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('is_primary', 1)
            ->first();

        if ($contact) {
            return response()->json([
                'success' => true,
                'message' => 'Contact fetched successfully',
                'data' => $contact
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No primary business contact found'
        ], 404);
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

    public function getBusinessContact($vendor_id, $business_id, $contact_id)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->find($contact_id);

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

    public function addBusinessContact(Request $request, $vendor_id, $business_id)
    {
        $validated = $request->validate([
            'contact_type' => 'required|string|in:phone,email,mobile,whatsapp,telegram,skype,other',
            'value' => 'required|string|max:255',
            'country_code' => 'nullable|string|max:10',
            'label' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
            'is_emergency' => 'nullable|boolean',
        ]);

        $vendor = Vendor::find($vendor_id);
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)
            ->where('id', $business_id)
            ->first();
        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'Business profile not found'
            ], 404);
        }

        // If setting as primary for phone or email, unset other primary contacts of the same contact_type
        if (($validated['is_primary'] ?? false) && in_array($validated['contact_type'], ['phone', 'email'])) {
            VendorContact::where('vendor_id', $vendor_id)
                ->where('business_id', $business_id)
                ->where('contact_type', $validated['contact_type'])
                ->update(['is_primary' => false]);
        }

        $contact = VendorContact::create([
            'tenant_id' => $vendor->tenant_id,
            'vendor_id' => $vendor_id,
            'business_id' => $business_id,
            'profile_type' => 'business',
            'contact_type' => $validated['contact_type'],
            'value' => $validated['value'],
            'country_code' => $validated['country_code'],
            'label' => $validated['label'],
            'is_primary' => $validated['is_primary'] ?? false,
            'is_emergency' => $validated['is_emergency'] ?? false,
        ]);

        // Sync primary_email or primary_phone in Vendor model
        if ($contact->is_primary && in_array($contact->contact_type, ['phone', 'email'])) {
            $updateData = [];
            if ($contact->contact_type === 'email') {
                $updateData['primary_email'] = $contact->value;
            } elseif ($contact->contact_type === 'phone') {
                $updateData['primary_phone'] = $contact->country_code ? $contact->country_code . $contact->value : $contact->value;
            }
            $vendor->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Business contact added successfully',
            'data' => $contact
        ]);
    }

    public function updateBusinessContact(Request $request, $vendor_id, $business_id, $contact_id)
    {
        $validated = $request->validate([
            'contact_type' => 'required|string|in:phone,email,mobile,whatsapp,telegram,skype,other',
            'value' => 'required|string|max:255',
            'country_code' => 'nullable|string|max:10',
            'label' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
            'is_emergency' => 'nullable|boolean',
        ]);

        $contact = VendorContact::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->find($contact_id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }

        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)
            ->where('id', $business_id)
            ->first();
        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'Business profile not found'
            ], 404);
        }

        // If setting as primary for phone or email, unset other primary contacts of the same contact_type
        if (($validated['is_primary'] ?? false) && in_array($validated['contact_type'], ['phone', 'email'])) {
            VendorContact::where('vendor_id', $vendor_id)
                ->where('business_id', $business_id)
                ->where('contact_type', $validated['contact_type'])
                ->where('id', '!=', $contact_id)
                ->update(['is_primary' => false]);
        }

        // Check if the contact was previously primary and is being unset
        $wasPrimary = $contact->is_primary;
        $isPrimaryNow = $validated['is_primary'] ?? false;

        $contact->update([
            'contact_type' => $validated['contact_type'],
            'value' => $validated['value'],
            'country_code' => $validated['country_code'],
            'label' => $validated['label'],
            'is_primary' => $isPrimaryNow,
            'is_emergency' => $validated['is_emergency'] ?? false,
        ]);

        // Sync primary_contact_email or primary_contact_phone in VendorBusinessProfile model
        if (in_array($validated['contact_type'], ['phone', 'email'])) {
            $updateData = [];
            if ($isPrimaryNow) {
                // Set primary_contact_email or primary_contact_phone
                if ($validated['contact_type'] === 'email') {
                    $updateData['primary_contact_email'] = $validated['value'];
                    // Debug: Log or verify the value being set
                    Log::info('Setting primary_contact_email to: ' . $validated['value']);
                } elseif ($validated['contact_type'] === 'phone') {
                    $updateData['primary_contact_phone'] = $validated['country_code'] ? $validated['country_code'] . $validated['value'] : $validated['value'];
                }
            } elseif ($wasPrimary && !$isPrimaryNow) {
                // Clear primary_contact_email or primary_contact_phone if no other primary contact exists for this type
                $hasOtherPrimary = VendorContact::where('vendor_id', $vendor_id)
                    ->where('business_id', $business_id)
                    ->where('contact_type', $validated['contact_type'])
                    ->where('id', '!=', $contact_id)
                    ->where('is_primary', true)
                    ->exists();

                if (!$hasOtherPrimary) {
                    if ($validated['contact_type'] === 'email') {
                        $updateData['primary_contact_email'] = null;
                        Log::info('Clearing primary_contact_email');
                    } elseif ($validated['contact_type'] === 'phone') {
                        $updateData['primary_contact_phone'] = null;
                    }
                }
            }
            if (!empty($updateData)) {
                $business->update($updateData);
                Log::info('Updated VendorBusinessProfile with: ', $updateData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Business contact updated successfully',
            'data' => $contact->fresh()
        ]);
    }
}
