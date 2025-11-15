<?php

namespace App\Http\Controllers\Vendors;

use Illuminate\Http\Request;
use App\Models\Vendor\Vendor;
use App\Models\VendorAddress;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Vendor\VendorBusinessProfile;

class VendorBusinessProfileController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddBusinessInfo', ['id' => $id]);
    }

    public function Businesslist($id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessList', ['id' => $id]);
    }

    public function BusinessDetails($id, $business_id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessDetails', ['id' => $id, 'business_id' => $business_id]);
    }

    public function manageBusiness($id)
    {
        return view('Admin.Vendors.VendorProfile.ManageBusinessinfo', ['id' => $id]);
    }


    public function addBusinessInfo(Request $request, $id)
    {
        // Get vendor and tenant_id
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid vendor ID.'
            ], 404);
        }

        // Validation rules
        $rules = [
            'legal_name'          => 'required|string|max:180',
            'trade_name'          => 'nullable|string|max:180',
            'industry'            => 'nullable|string|max:120',
            'incorporation_date'  => 'nullable|date',
            'business_size'       => 'nullable|in:micro,sme,enterprise',
            'website'             => 'nullable|url|max:200',
            'billing_email'       => 'nullable|email|max:150',
            'billing_phone'       => 'nullable|string|max:30',
            'gst_number'          => 'nullable|string|max:15',
            'pan_number'          => 'nullable|string|max:15',
            'cin_number'          => 'nullable|string|max:25',
            'credit_limit'        => 'nullable|numeric|min:0',
            'payment_terms_days'  => 'nullable|integer|min:0',
            'account_manager'     => 'nullable|string|max:120',
        ];

        $validatedData = $request->validate($rules);

        try {
            $profile = VendorBusinessProfile::create(array_merge($validatedData, [
                'vendor_id' => $id,
                'tenant_id' => $vendor->tenant_id
            ]));

            $vendor->type = 'business';
            $vendor->save();

            return response()->json([
                'success' => true,
                'message' => 'Business profile added successfully.',
                'data' => $profile
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add business profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function allBusinessPage()
    {
        return view('Admin.Vendors.VendorProfile.AllBusiness');
    }

    public function totalBusiness(Request $request)
    {
        $query = Vendor::query()
            ->where('type', 'business')
            ->with('businessProfile');

        // Apply filter if vendor_uid is provided
        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
            $query->where('vendor_uid', $request->vendor_id);
        }

        $query->select('vendors.*');

        return DataTables::of($query)
            ->addColumn('vendor_name', function ($vendor) {
                return $vendor->vendor_uid;
            })
            ->addColumn('legal_name', function ($vendor) {
                return $vendor->businessProfile?->legal_name ?? '-';
            })
            ->addColumn('trade_name', function ($vendor) {
                return $vendor->businessProfile?->trade_name ?? '-';
            })
            ->addColumn('action', function ($vendor) {
                return '<a href="' . route('vendors.viewDetails', $vendor->id) . '" class="btn btn-sm btn-primary">View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function manage($id, $business_id)
    {
        return view('Admin.Vendors.VendorProfile.ManageBusinessinfo', ['id' => $id, 'business_id' => $business_id]);
    }

    public function fetchBusinessDetails($id, $business_id)
    {
        $profile = VendorBusinessProfile::where('vendor_id', $id)->where('id', $business_id)->first();
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

    public function allBusiness($id)
    {
        $profiles = VendorBusinessProfile::where('vendor_id', $id)->get();
        return response()->json([
            'success' => true,
            'data' => $profiles
        ]);
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
