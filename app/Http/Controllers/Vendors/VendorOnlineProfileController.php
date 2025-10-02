<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorOnileProfile;
use App\Models\VendorOnilneProfile;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;

class VendorOnlineProfileController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddOnlineProfiles', ['id' => $id]);
    }

    public function store(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $request->validate([
            'social_platform.*' => 'required|string|max:191',
            'icon.*'            => 'nullable|string|max:191',
            'profile_url.*'    => 'required|url|max:191',
        ]);

        $profiles = [];

        foreach ($request->social_platform as $index => $platform) {
            $profileData = [
                'tenant_id'       => $vendor->tenant_id,
                'vendor_id'       => $vendor_id,
                'profile_type'    => $vendor->type,
                'social_platform' => $platform,
                'icon'            => $request->icon[$index] ?? null,
                'profile_url'     => $request->profile_url[$index],
                'created_at'      => now(),
                'updated_at'      => now(),
            ];

            if ($vendor->type === 'business') {
                $business = VendorBusinessProfile::where('vendor_id', $vendor->id)->first();

                if (!$business) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Business profile not found for this vendor.'
                    ], 422);
                }

                $profileData['business_id']   = $business->id;
                $profileData['business_name'] = $business->trade_name;
            }

            $profiles[] = $profileData;
        }

        VendorOnilneProfile::insert($profiles);

        return response()->json([
            'success' => true,
            'message' => 'All Steps are Complete Successfully'
        ]);
    }

    public function manage($id, $type)
    {
        return view('Admin.Vendors.VendorProfile.ManageOnlineProfile', ['id' => $id, 'type' => $type]);
    }

    public function list($id, $type)
    {
        $profiles = VendorOnilneProfile::where('vendor_id', $id)->where('profile_type', $type)->get();
        return response()->json([
            'success' => true,
            'data' => $profiles
        ]);
    }

    public function storeOne(Request $request, $vendor_id, $type)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $validated = $request->validate([
            'social_platform' => 'required|string|max:191',
            'icon'            => 'nullable|string|max:191',
            'profile_url'     => 'required|url|max:191',
        ]);

        $data = [
            'tenant_id'       => $vendor->tenant_id,
            'vendor_id'       => $vendor_id,
            'profile_type'    => $type,
            'social_platform' => $validated['social_platform'],
            'icon'            => $validated['icon'] ?? null,
            'profile_url'     => $validated['profile_url'],
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

        $profile = VendorOnilneProfile::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Online profile created',
            'data'    => $profile
        ], 201);
    }

    public function update(Request $request, $vendor_id, $type, VendorOnilneProfile $profile)
    {
        $validated = $request->validate([
            'social_platform' => 'required|string|max:191',
            'icon'            => 'nullable|string|max:191',
            'profile_url'     => 'required|url|max:191',
        ]);

        $profile->update([
            'social_platform' => $validated['social_platform'],
            'icon'            => $validated['icon'] ?? null,
            'profile_url'     => $validated['profile_url'],
            'profile_type'    => $type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Online profile updated',
            'data'    => $profile
        ]);
    }

    public function destroy($vendor_id, $type, VendorOnilneProfile $profile)
    {
        $profile->delete();
        return response()->json([
            'success' => true,
            'message' => 'Online profile deleted'
        ]);
    }
}
