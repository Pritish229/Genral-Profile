<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorAddress;
use App\Models\VendorContact;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;
use App\Models\VendorIndividualProfile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function create()
    {
        return view('Admin.Vendors.VendorProfile.AddVendor');
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'               => 'required|in:individual,business',
            'primary_email'      => 'required|email|unique:vendors,primary_email',
            'primary_phone'      => 'nullable|string|max:20',
            'first_name'         => 'required_if:type,individual|string|max:100',
            'middle_name'        => 'nullable|string|max:100',
            'last_name'          => 'required_if:type,individual|string|max:100',
            'dob'                => 'nullable|date',
            'gender'             => 'nullable|in:male,female,other',
            'marital_status'     => 'nullable|in:single,married,divorced,widowed,other',
            'preferred_currency' => 'nullable|string|max:3',
            'vendor_uid'         => 'nullable|string|unique:vendors,vendor_uid',
            'onboarding_channel' => 'nullable|string|max:255',
            'occupation'         => 'nullable|string|max:255',
            'nationality'        => 'nullable|string|max:100',
            'preferred_language' => 'nullable|string|max:100',
            'preferred_currency' => 'nullable|string|max:3',
        ]);

        $vendor = Vendor::create([
            'vendor_uid'    => $validated['vendor_uid'],
            'tenant_id'     => '1',
            'type'          => $validated['type'],
            'primary_email' => $validated['primary_email'],
            'primary_phone' => $validated['primary_phone'] ?? null,
        ]);

        if ($vendor) {
            VendorContact::create([
                'vendor_id'    => $vendor->id,
                'tenant_id'    => '1',
                'contact_type' => 'phone',
                'value'        => $validated['primary_phone'] ?? null,
                'is_primary'   => true,
            ]);

            VendorContact::create([
                'vendor_id'    => $vendor->id,
                'tenant_id'    => '1',
                'contact_type' => 'email',
                'value'        => $validated['primary_email'],
                'is_primary'   => true,
            ]);
        }

        // Handle individual profile
        $profile = VendorIndividualProfile::create([
            'vendor_id'          => $vendor->id,
            'tenant_id'          => '1',
            'first_name'         => $validated['first_name'],
            'middle_name'        => $validated['middle_name'] ?? null,
            'last_name'          => $validated['last_name'],
            'type'               => $vendor->type,
            'dob'                => $validated['dob'] ?? null,
            'gender'             => $validated['gender'] ?? null,
            'marital_status'     => $validated['marital_status'] ?? null,
            'occupation'         => $validated['occupation'] ?? null,
            'nationality'        => $validated['nationality'] ?? null,
            'preferred_language' => $validated['preferred_language'] ?? null,
            'preferred_currency' => $validated['preferred_currency'] ?? null,
        ]);


        // Handle avatar upload only for individuals
        if ($request->hasFile('avatar_url')) {
            $file = $request->file('avatar_url');
            $extension = $file->getClientOriginalExtension();
            $fileName = ($validated['vendor_uid'] ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('VendorImages', $fileName, 'public');

            $profile->update([
                'avatar_url' => "VendorImages/{$fileName}",
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Vendor created successfully',
            'data'    => $vendor,
        ]);
    }

    public function manage($id)
    {
        return view('Admin.Vendors.VendorProfile.ManageVendor', [
            'id' => $id
        ]);
    }

    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);

        // Only get individual profile for this management page
        $profile = VendorIndividualProfile::where('vendor_id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'vendor' => $vendor,
                'profile' => $profile
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $validated = $request->validate([
            'primary_email'      => 'nullable|email|unique:vendors,primary_email,' . $id,
            'primary_phone'      => 'nullable|string|max:20',
            'first_name'         => 'required|string|max:100',
            'middle_name'        => 'nullable|string|max:100',
            'last_name'          => 'required|string|max:100',
            'dob'                => 'nullable|date',
            'gender'             => 'nullable|in:male,female,other,unspecified',
            'marital_status'     => 'nullable|in:single,married,divorced,widowed,other',
            'vendor_uid'         => 'nullable|string|unique:vendors,vendor_uid,' . $id,
            'onboarding_channel' => 'nullable|string|max:255',
            'occupation'         => 'nullable|string|max:255',
            'nationality'        => 'nullable|string|max:100',
            'preferred_language' => 'nullable|string|max:100',
            'preferred_currency' => 'nullable|string|max:3',
            'status'             => 'nullable|in:active,inactive,suspended',
            'notes'              => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
    
            // Update vendor record
            $vendor->update([
                'vendor_uid'         => $validated['vendor_uid'] ?? $vendor->vendor_uid,
                'onboarding_channel' => $validated['onboarding_channel'] ?? $vendor->onboarding_channel,
                'status'             => $validated['status'] ?? $vendor->status,
                'notes'              => $validated['notes'] ?? $vendor->notes,
            ]);
            $profile = VendorIndividualProfile::firstOrCreate(
                ['vendor_id' => $id],
                [
                    'tenant_id' => $vendor->tenant_id,
                ]
            );
            $profile->update([
                'first_name'         => $validated['first_name'],
                'middle_name'        => $validated['middle_name'],
                'last_name'          => $validated['last_name'],
                'dob'                => $validated['dob'],
                'gender'             => $validated['gender'],
                'marital_status'     => $validated['marital_status'],
                'occupation'         => $validated['occupation'],
                'nationality'        => $validated['nationality'],
                'preferred_language' => $validated['preferred_language'],
                'preferred_currency' => $validated['preferred_currency'],
            ]);

            // Handle avatar upload
            if ($request->hasFile('avatar_url')) {
                // Delete old avatar if exists
                if ($profile->avatar_url && Storage::disk('public')->exists($profile->avatar_url)) {
                    Storage::disk('public')->delete($profile->avatar_url);
                }

                $file = $request->file('avatar_url');
                $extension = $file->getClientOriginalExtension();
                $fileName = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
                $file->storeAs('VendorImages', $fileName, 'public');

                $profile->update([
                    'avatar_url' => "VendorImages/{$fileName}",
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vendor updated successfully',
                'data' => $vendor->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update vendor: ' . $e->getMessage()
            ], 500);
        }
    }

}
