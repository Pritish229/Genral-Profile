<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorOnlineProfile;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;

class VendorOnlineProfileController extends Controller
{
    // === Individual-Specific Functions ===

    public function individualIndex($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('Admin.Vendors.VendorProfile.AddOnlineProfiles', ['id' => $id, 'type' => 'individual']);
    }

    public function individualManage($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('Admin.Vendors.VendorProfile.ManageOnlineProfile', ['id' => $id, 'type' => 'individual']);
    }

    public function individualStore(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        // Removed type check - allow any vendor type, but enforce profile_type logic
        // Optionally add: if ($vendor->type === 'business') { /* handle or restrict */ }

        if ($request->filled('business_id')) {
            return response()->json(['success' => false, 'message' => 'business_id not allowed for individual.'], 422);
        }

        $submitting = $request->has('social_platform') && is_array($request->social_platform) && count($request->social_platform) > 0;

        if ($submitting) {
            $request->validate([
                'social_platform.*' => 'required|string|max:191',
                'icon.*' => 'nullable|string|max:191',
                'profile_url.*' => 'required|url|max:191',
            ]);

            $profiles = [];
            foreach ($request->social_platform as $i => $platform) {
                $profiles[] = [
                    'tenant_id' => $vendor->tenant_id,
                    'vendor_id' => $vendor_id,
                    'profile_type' => 'individual',
                    'social_platform' => $platform,
                    'icon' => $request->icon[$i] ?? null,
                    'profile_url' => $request->profile_url[$i],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            VendorOnlineProfile::insert($profiles);
        }

        return response()->json(['success' => true, 'message' => 'Step completed (skipped if empty)']);
    }

    public function individualList($id)
    {
        $vendor = Vendor::findOrFail($id);
        // Removed type check
        $profiles = VendorOnlineProfile::where('vendor_id', $id)->where('profile_type', 'individual')->get();

        return response()->json(['success' => true, 'data' => $profiles]);
    }

    public function individualStoreOne(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        // Removed type check

        if ($request->filled('business_id')) {
            return response()->json(['success' => false, 'message' => 'business_id not allowed.'], 422);
        }

        $validated = $request->validate([
            'social_platform' => 'required|string|max:191',
            'icon' => 'nullable|string|max:191',
            'profile_url' => 'required|url|max:191',
        ]);

        $data = [
            'tenant_id' => $vendor->tenant_id,
            'vendor_id' => $vendor_id,
            'profile_type' => 'individual',
            'social_platform' => $validated['social_platform'],
            'icon' => $validated['icon'] ?? null,
            'profile_url' => $validated['profile_url'],
        ];

        $profile = VendorOnlineProfile::create($data);

        return response()->json(['success' => true, 'message' => 'Created', 'data' => $profile], 201);
    }

    public function individualUpdate(Request $request, $vendor_id, VendorOnlineProfile $profile)
    {
        if ($profile->vendor_id != $vendor_id || $profile->profile_type != 'individual') {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $validated = $request->validate([
            'social_platform' => 'required|string|max:191',
            'icon' => 'nullable|string|max:191',
            'profile_url' => 'required|url|max:191',
        ]);

        $profile->update($validated);

        return response()->json(['success' => true, 'message' => 'Updated', 'data' => $profile->fresh()]);
    }

    public function individualDestroy($vendor_id, VendorOnlineProfile $profile)
    {
        if ($profile->vendor_id != $vendor_id || $profile->profile_type != 'individual') {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $profile->delete();

        return response()->json(['success' => true, 'message' => 'Deleted']);
    }

    // === Business-Specific Functions ===

    public function businessIndex($id, $business_id)
    {
        
        return view('Admin.Vendors.VendorProfile.ManageBusinessOnlineProfile', ['id' => $id, 'business_id' => $business_id, 'type' => 'business']);
    }

    public function businessManage($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('Admin.Vendors.VendorProfile.ManageBusinessOnlineProfile', ['id' => $id, 'type' => 'business']);
    }

    public function businessStore(Request $request, $vendor_id, $business_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        // Removed type check

        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)->findOrFail($business_id);

        $submitting = $request->has('social_platform') && is_array($request->social_platform) && count($request->social_platform) > 0;

        if ($submitting) {
            $request->validate([
                'social_platform.*' => 'required|string|max:191',
                'icon.*' => 'nullable|string|max:191',
                'profile_url.*' => 'required|url|max:191',
            ]);

            $profiles = [];
            foreach ($request->social_platform as $i => $platform) {
                $profiles[] = [
                    'tenant_id' => $vendor->tenant_id,
                    'vendor_id' => $vendor_id,
                    'profile_type' => 'business',
                    'business_id' => $business->id,
                    'business_name' => $business->trade_name,
                    'social_platform' => $platform,
                    'icon' => $request->icon[$i] ?? null,
                    'profile_url' => $request->profile_url[$i],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            VendorOnlineProfile::insert($profiles);
        }

        return response()->json(['success' => true, 'message' => 'Step completed (skipped if empty)']);
    }

    public function businessList($id, $business_id)
    {
        $vendor = Vendor::find($id);
        
        if($vendor){
            $query = VendorOnlineProfile::where('vendor_id', $id)->where('business_id',$business_id)->where('profile_type', 'business')->get();
            return response()->json(['success' => true, 'data' => $query]);
        } else {
            return response()->json(['success' => false, 'message' => 'Vendor not found.'], 404);
        }
        

    }

    public function businessStoreOne(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        // Removed type check

        $request->validate(['business_id' => 'required|integer|exists:vendor_business_profiles,id']);
        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)->findOrFail($request->business_id);

        $validated = $request->validate([
            'social_platform' => 'required|string|max:191',
            'icon' => 'nullable|string|max:191',
            'profile_url' => 'required|url|max:191',
        ]);

        $data = [
            'tenant_id' => $vendor->tenant_id,
            'vendor_id' => $vendor_id,
            'profile_type' => 'business',
            'business_id' => $business->id,
            'business_name' => $business->trade_name,
            'social_platform' => $validated['social_platform'],
            'icon' => $validated['icon'] ?? null,
            'profile_url' => $validated['profile_url'],
        ];

        $profile = VendorOnlineProfile::create($data);

        return response()->json(['success' => true, 'message' => 'Created', 'data' => $profile], 201);
    }

    public function businessUpdate(Request $request, $vendor_id, VendorOnlineProfile $profile)
    {
        if ($profile->vendor_id != $vendor_id || $profile->profile_type != 'business') {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $validated = $request->validate([
            'social_platform' => 'required|string|max:191',
            'icon' => 'nullable|string|max:191',
            'profile_url' => 'required|url|max:191',
        ]);

        $profile->update($validated);

        return response()->json(['success' => true, 'message' => 'Updated', 'data' => $profile->fresh()]);
    }

    public function businessDestroy($vendor_id, VendorOnlineProfile $profile)
    {
        if ($profile->vendor_id != $vendor_id || $profile->profile_type != 'business') {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $profile->delete();

        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}