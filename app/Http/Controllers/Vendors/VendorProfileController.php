<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VendorIndividualProfile;
use Yajra\DataTables\Facades\DataTables;

class VendorProfileController extends Controller
{
    public function Details($id)
    {
        $data = VendorIndividualProfile::where('vendor_id', $id)->first();
        $vendors = Vendor::where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $data,
            'primary_details' => $vendors
        ], 200);
    }

    public function viewDetails($vendor_id)
    {
        return view('Admin.Vendors.VendorProfile.VendorsDetails', ['id' => $vendor_id]);
    }

    public function listAll()
{
    $vendors = Vendor::with(['individualProfile', 'businessProfile'])->select('vendors.*');

    // Helper closure to get the profile
    $getProfile = function ($vendor) {
        return $vendor->individualProfile ?? $vendor->businessProfile;
    };

    return DataTables::of($vendors)
        ->addColumn('avatar', function ($vendor) use ($getProfile) {
            $profile = $getProfile($vendor);
            if ($profile && $profile->avatar_url) {
                return '<img src="' . asset('storage/' . $profile->avatar_url) . '" width="50" height="50" />';
            }
            return '-';
        })
        ->addColumn('full_name', function ($vendor) use ($getProfile) {
            $profile = $getProfile($vendor);
            if ($profile) {
                $nameParts = array_filter([
                    $profile->first_name ?? '',
                    $profile->middle_name ?? '',
                    $profile->last_name ?? ''
                ]);
                return implode(' ', $nameParts);
            }
            return '-';
        })
        ->addColumn('roll_no', function ($vendor) use ($getProfile) {
            $profile = $getProfile($vendor);
            return $profile->roll_no ?? '-';
        })
        ->addColumn('class_section', function ($vendor) use ($getProfile) {
            $profile = $getProfile($vendor);
            return ($profile && isset($profile->current_class, $profile->section))
                ? ($profile->current_class . ' / ' . $profile->section)
                : '-';
        })
        ->addColumn('vendor_uid', function ($vendor) {
            return $vendor->vendor_uid ?? '-';
        })
        ->addColumn('primary_email', function ($vendor) {
            return $vendor->primary_email ?? '-';
        })
        ->addColumn('primary_phone', function ($vendor) {
            return $vendor->primary_phone ?? '-';
        })
        ->addColumn('status', function ($vendor) {
            return ucfirst($vendor->status ?? '-');
        })
        ->addColumn('hire_date', function ($vendor) {
            return $vendor->hire_date ? date('d-M-Y', strtotime($vendor->hire_date)) : '-';
        })
        ->addColumn('actions', function ($vendor) {
            $url = route("vendors.viewDetails", $vendor->id);
            return '<a href="' . $url . '" class="btn btn-sm btn-success">Details</a>';
        })
        ->filter(function ($query) {
            if (request()->has('search') && !empty(request('search')['value'])) {
                $search = request('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('vendor_uid', 'like', "%{$search}%")
                      ->orWhere('primary_phone', 'like', "%{$search}%")
                      ->orWhereHas('individualProfile', function ($q2) use ($search) {
                          $q2->where('first_name', 'like', "%{$search}%")
                             ->orWhere('middle_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('businessProfile', function ($q2) use ($search) {
                          $q2->where('first_name', 'like', "%{$search}%")
                             ->orWhere('middle_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            }
        })
        ->rawColumns(['avatar', 'actions'])
        ->make(true);
}

}
