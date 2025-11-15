<?php

namespace App\Http\Controllers\Customers;

use App\Models\Customer\Customer;
use App\Models\Customer\CustomerBusinessProfile;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CustomerBusinessProfileController extends Controller
{
    public function index($id)
    {
        return view('Admin.Customers.CustomerProfile.AddBusinessInfo', ['id' => $id]);
    }

    public function Businesslist($id)
    {
        return view('Admin.Customers.CustomerProfile.BusinessList', ['id' => $id]);
    }

    public function BusinessDetails($id, $business_id)
    {
        return view('Admin.Customers.CustomerProfile.BusinessDetails', ['id' => $id, 'business_id' => $business_id]);
    }

    public function manageBusiness($id)
    {
        return view('Admin.Customers.CustomerProfile.ManageBusinessinfo', ['id' => $id]);
    }


    public function addBusinessInfo(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid customer ID.'
            ], 404);
        }

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

        DB::beginTransaction();

        try {
            $profile = CustomerBusinessProfile::create(array_merge($validatedData, [
                'customer_id' => $id,
                'tenant_id' => $customer->tenant_id
            ]));

            if ($customer->type !== 'business') {
                $customer->type = 'business';
                $customer->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Business profile added successfully.',
                'data' => [
                    'profile' => $profile,
                    'customer' => $customer
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add business profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function allBusinessPage()
    {
        return view('Admin.customers.customerProfile.AllBusiness');
    }

    public function totalBusiness(Request $request)
    {
        $query = customer::query()
            ->where('type', 'business')
            ->with('businessProfile');

        // Apply filter if customer_uid is provided
        if ($request->has('customer_id') && !empty($request->customer_id)) {
            $query->where('customer_uid', $request->customer_id);
        }

        $query->select('customers.*');

        return DataTables::of($query)
            ->addColumn('customer_name', function ($customer) {
                return $customer->customer_uid;
            })
            ->addColumn('legal_name', function ($customer) {
                return $customer->businessProfile?->legal_name ?? '-';
            })
            ->addColumn('trade_name', function ($customer) {
                return $customer->businessProfile?->trade_name ?? '-';
            })
            ->addColumn('action', function ($customer) {
                return '<a href="' . route('customers.viewDetails', $customer->id) . '" class="btn btn-sm btn-primary">View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function manage($id, $business_id)
    {
        return view('Admin.customers.customerProfile.ManageBusinessinfo', ['id' => $id, 'business_id' => $business_id]);
    }

    public function fetchBusinessDetails($id, $business_id)
    {
        $profile = CustomerBusinessProfile::where('customer_id', $id)->where('id', $business_id)->first();
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
        $profiles = CustomerBusinessProfile::where('customer_id', $id)->get();
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

        $profile = CustomerBusinessProfile::where('customer_id', $id)->first();
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
