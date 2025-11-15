<?php

namespace App\Http\Controllers\Vendors;

use Illuminate\Http\Request;
use App\Models\Vendor\Vendor;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Vendor\VendorBusinessProfile;
use App\Models\Vendor\VendorIndividualProfile;

class VendorProfileController extends Controller
{

    public function vendorlist()
    {
        return view('Admin.Vendors.VendorProfile.VendorsList',);
    }
    public function Details($id)
    {
        $vendor = Vendor::where('id', $id)->first();

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        // Always get individual profile for main details
        $data = VendorIndividualProfile::where('vendor_id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $data,
            'primary_details' => $vendor
        ], 200);
    }

    public function Business($id)
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

    public function viewDetails($vendor_id)
    {
        return view('Admin.Vendors.VendorProfile.VendorsDetails', ['id' => $vendor_id]);
    }

    public function listAll()
    {
        $vendors = Vendor::with('individualProfile')->select('vendors.*');

        return DataTables::of($vendors)
            ->addColumn('avatar', function ($vendor) {
                $profile = $vendor->individualProfile;
                if ($profile && $profile->avatar_url) {
                    return '<img src="' . asset('storage/' . $profile->avatar_url) . '" 
                            alt="Avatar" width="50" height="50" 
                            class="rounded-circle shadow-sm" />';
                }
                return '<span class="text-muted">N/A</span>';
            })
            ->addColumn('full_name', function ($vendor) {
                return $vendor->individualProfile->full_name ?? '<span class="text-muted">N/A</span>';
            })
            ->addColumn('vendor_uid', fn($vendor) => $vendor->vendor_uid ?? '-')
            ->addColumn('type', fn($vendor) => ucfirst($vendor->type ?? '-'))
            ->addColumn('primary_email', fn($vendor) => $vendor->primary_email ?? '-')
            ->addColumn('primary_phone', fn($vendor) => $vendor->primary_phone ?? '-')
            ->addColumn('status', fn($vendor) => ucfirst($vendor->status ?? '-'))
            ->addColumn(
                'hire_date',
                fn($vendor) =>
                $vendor->hire_date ? date('d-M-Y', strtotime($vendor->hire_date)) : '-'
            )
            ->addColumn('actions', function ($vendor) {
                $url = route("vendors.viewDetails", $vendor->id);
                return '<a href="' . $url . '" class="btn btn-sm btn-success">Details</a>';
            })
            ->filter(function ($query) {
                if (request()->has('search') && !empty(request('search')['value'])) {
                    $search = request('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('vendor_uid', 'like', "%{$search}%")
                            ->orWhere('primary_email', 'like', "%{$search}%")
                            ->orWhere('primary_phone', 'like', "%{$search}%")
                            ->orWhereHas('individualProfile', function ($sub) use ($search) {
                                $sub->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->rawColumns(['avatar', 'actions', 'full_name'])
            ->make(true);
    }
}
