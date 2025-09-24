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

class VendorController extends Controller
{
    public function create()
    {
        return view('Admin.Vendors.VendorProfile.AddVendor');
    }
    public function vendorlist()
    {
        return view('Admin.Vendors.VendorProfile.VendorsList',);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'            => 'required|in:individual,business',
            'primary_email'   => 'required|email|unique:vendors,primary_email',
            'primary_phone'    => 'nullable|string|max:20',
            'first_name'      => 'required_if:type,individual|string|max:100',
            'middle_name'     => 'nullable|string|max:100',
            'last_name'       => 'required_if:type,individual|string|max:100',
            'dob'             => 'nullable|date',
            'gender'          => 'nullable|in:male,female,other',
            'marital_status'  => 'nullable|in:single,married,divorced,widowed,other',
            'preferred_currency' => 'nullable|string|max:3',
            'vendor_uid' => 'nullable|string|unique:vendors,vendor_uid',
            'onboarding_channel' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'preferred_language' => 'nullable|string|max:100',
            'preferred_currency' => 'nullable|string|max:3',
        ]);

        $vendor = Vendor::create([
            'vendor_uid' => $validated['vendor_uid'],
            'tenant_id'     => '1',
            'type'          => $validated['type'],
            'primary_email' => $validated['primary_email'],
            'primary_phone'  => $validated['primary_phone'] ?? null,
        ]);

        if ($vendor->type === 'individual') {
            $profile = VendorIndividualProfile::create([
                'vendor_id'      => $vendor->id,
                'tenant_id'      => '1',
                'first_name'     => $validated['first_name'],
                'middle_name'    => $validated['middle_name'] ?? null,
                'last_name'      => $validated['last_name'],
                'dob'            => $validated['dob'] ?? null,
                'gender'         => $validated['gender'] ?? null,
                'marital_status' => $validated['marital_status'] ?? null,
                'occupation'    => $validated['occupation'] ?? null,
                'nationality' => $validated['nationality'] ?? null,
                'preferred_language' => $validated['preferred_language'] ?? null,
                'preferred_currency' => $validated['preferred_currency'] ?? null,

            ]);
        }

        if ($request->hasFile('avatar_url')) {
            $file = $request->file('avatar_url');
            $extension = $file->getClientOriginalExtension();
            $fileName = ($validated['vendor_uid'] ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('vendorImages', $fileName, 'public');
            $profile->update(['avatar_url' => "VendorImages/{$fileName}",]);
        }

        return response()->json(['success' => true, 'message' => 'Vendor created successfully', 'data' => $vendor]);
    }
}
