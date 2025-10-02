<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorAddress;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;

class VendorBusinessProfileController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddBusinessInfo', ['id' => $id]);
    }

    public function manageBusiness($id)
    {
        return view('Admin.Vendors.VendorProfile.ManageBusinessinfo', ['id' => $id]);
    }


    public function addBusinessInfo(Request $request, $id)
    {
        // Step 1 — Validate request
        $request->validate([
            'legal_name' => 'nullable|string|max:180',
            'trade_name' => 'nullable|string|max:180',
            'industry' => 'nullable|string|max:120',
            'business_size' => 'nullable|in:micro,sme,enterprise',
            'incorporation_date' => 'nullable|date',
            'website' => 'nullable|url|max:200',
            'primary_contact_name' => 'nullable|string|max:150',
            'primary_contact_email' => 'nullable|email|max:150',
            'primary_contact_phone' => 'nullable|string|max:30',
            'billing_email' => 'nullable|email|max:150',
            'billing_phone' => 'nullable|string|max:30',
            'gst_number' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:15',
            'cin_number' => 'nullable|string|max:25',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'nullable|integer|min:0',
            'account_manager' => 'nullable|string|max:120',

            // Address validation
            'state' => 'nullable|string|max:120',
            'district' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'pincode' => 'nullable|string|max:12',
            'line1' => 'nullable|string|max:180',
            'line2' => 'nullable|string|max:180',
            'landmark' => 'nullable|string|max:150',
            'label' => 'nullable|string|max:120',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();


            // Step 2 — Save Business Profile
            $businessProfile = VendorBusinessProfile::updateOrCreate(
                ['vendor_id' => $id, 'tenant_id' => 1],
                $request->only([
                    'legal_name',
                    'trade_name',
                    'industry',
                    'business_size',
                    'incorporation_date',
                    'website',
                    'primary_contact_name',
                    'primary_contact_email',
                    'primary_contact_phone',
                    'billing_email',
                    'billing_phone',
                    'gst_number',
                    'pan_number',
                    'cin_number',
                    'credit_limit',
                    'payment_terms_days',
                    'account_manager'
                ])
            );

            // Step 3 — Save Address
            VendorAddress::create([
                'vendor_id' => $id,
                'tenant_id' => 1,
                'business_id' => $businessProfile->id,
                'business_name' => $businessProfile->legal_name,
                'profile_type' => 'business',
                'address_type' => 'office', // you can make it dynamic if needed
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
                'is_primary' => true, // you can make dynamic
                'is_verified' => false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Business info and address saved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function manage($id)
    {
        return view('Admin.Vendors.VendorProfile.ManageBusinessinfo', ['id' => $id]);
    }

    public function getBusiness($id)
    {
        $profile = VendorBusinessProfile::where('vendor_id', $id)->first();
        if ($profile) {
            return response()->json([
                'success' => true,
                'data' => $profile
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No business profile found.'
            ], 404);
        }
    }

    public function updateBusiness(Request $request, $id)
    {
        $validated = $request->validate([
            'legal_name' => 'nullable|string|max:180',
            'trade_name' => 'nullable|string|max:180',
            'industry' => 'nullable|string|max:120',
            'business_size' => 'nullable|in:micro,sme,enterprise',
            'incorporation_date' => 'nullable|date',
            'website' => 'nullable|url|max:200',
            'primary_contact_name' => 'nullable|string|max:150',
            'primary_contact_email' => 'nullable|email|max:150',
            'primary_contact_phone' => 'nullable|string|max:30',
            'billing_email' => 'nullable|email|max:150',
            'billing_phone' => 'nullable|string|max:30',
            'gst_number' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:15',
            'cin_number' => 'nullable|string|max:25',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'nullable|integer|min:0',
            'account_manager' => 'nullable|string|max:120',
        ]);

        $profile = VendorBusinessProfile::where('vendor_id', $id)->first();
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Business profile not found.'
            ], 404);
        }

        $profile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business profile updated successfully.',
            'data' => $profile->fresh()
        ]);
    }
}
