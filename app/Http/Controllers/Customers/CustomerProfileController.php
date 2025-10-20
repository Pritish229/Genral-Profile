<?php

namespace App\Http\Controllers\Customers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerBusinessProfile;
use Yajra\DataTables\Facades\DataTables;
use App\Models\CustomerIndividualProfile;

class CustomerProfileController extends Controller
{
    public function customerlist()
    {
        return view('Admin.Customers.customerProfile.CustomersList',);
    }
    public function Details($id)
    {
        $customer = Customer::where('id', $id)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'customer not found'
            ], 404);
        }

        // Always get individual profile for main details
        $data = CustomerIndividualProfile::where('customer_id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $data,
            'primary_details' => $customer
        ], 200);
    }

    public function Business($id)
    {
        $profile = CustomerBusinessProfile::where('customer_id', $id)->first();
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

    public function viewDetails($customer_id)
    {
        return view('Admin.customers.customerProfile.CustomersDetails', ['id' => $customer_id]);
    }

    public function listAll()
    {
        $customers = Customer::with('individualProfile')->select('customers.*');

        return DataTables::of($customers)
            ->addColumn('avatar', function ($customer) {
                $profile = $customer->individualProfile;
                if ($profile && $profile->avatar_url) {
                    return '<img src="' . asset('storage/' . $profile->avatar_url) . '" 
                            alt="Avatar" width="50" height="50" 
                            class="rounded-circle shadow-sm" />';
                }
                return '<span class="text-muted">N/A</span>';
            })
            ->addColumn('full_name', function ($customer) {
                return $customer->individualProfile->full_name ?? '<span class="text-muted">N/A</span>';
            })
            ->addColumn('customer_uid', fn($customer) => $customer->customer_uid ?? '-')
            ->addColumn('type', fn($customer) => ucfirst($customer->type ?? '-'))
            ->addColumn('primary_email', fn($customer) => $customer->primary_email ?? '-')
            ->addColumn('primary_phone', fn($customer) => $customer->primary_phone ?? '-')
            ->addColumn('status', fn($customer) => ucfirst($customer->status ?? '-'))
            ->addColumn(
                'hire_date',
                fn($customer) =>
                $customer->hire_date ? date('d-M-Y', strtotime($customer->hire_date)) : '-'
            )
            ->addColumn('actions', function ($customer) {
                $url = route("customers.viewDetails", $customer->id);
                return '<a href="' . $url . '" class="btn btn-sm btn-success">Details</a>';
            })
            ->filter(function ($query) {
                if (request()->has('search') && !empty(request('search')['value'])) {
                    $search = request('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('customer_uid', 'like', "%{$search}%")
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
