<?php

namespace App\Http\Controllers\Customers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerPaymentAccount;
use App\Models\CustomerBusinessProfile;

class CustomerBankController extends Controller
{
    public function index($id)
    {
        return view('Admin.Customers.CustomerProfile.AddBankinfo', ['id' => $id]);
    }

    public function ManageBank($id)
    {
        return view('Admin.Customers.CustomerProfile.ManageBank', ['id' => $id]);
    }

    public function businessBank($id, $business_id)
    {
        return view('Admin.Customers.CustomerProfile.BusinessBank', ['id' => $id, 'business_id' => $business_id]);
    }

    public function saveBank(Request $request, $customer_id, $business_id = null)
    {
        $customer = Customer::findOrFail($customer_id);

        $rules = ['method' => 'required|in:bank,upi'];

        if ($request->method === 'bank') {
            $rules += [
                'account_holder'    => 'required|string|max:191',
                'bank_name'         => 'required|string|max:191',
                'account_number'    => 'required|string|max:50',
                'branch_name'       => 'nullable|string|max:191',
                'ifsc_code'         => 'required|string|max:20',
                'swift_code'        => 'nullable|string|max:50',
                'is_default_payout' => 'nullable|in:0,1',
            ];
        } elseif ($request->method === 'upi') {
            $rules += [
                'upi_name'          => 'required|string|max:191',
                'upi_id'            => 'required|string|max:191',
                'is_default_payout' => 'nullable|in:0,1',
            ];
        }

        $validated = $request->validate($rules);

        $is_primary        = filter_var($request->input('is_primary', 0), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $is_default_payout = filter_var($request->input('is_default_payout', 0), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        $account_number       = null;
        $account_number_mask  = null;
        $account_number_hash  = null;

        if ($validated['method'] === 'bank') {
            $account_number = substr(preg_replace('/\D/', '', $validated['account_number']), 0, 20);
            $account_number_mask = substr(str_repeat('X', max(0, strlen($account_number) - 4)) . substr($account_number, -4), 0, 20);
            $account_number_hash = hash('sha256', $account_number);
        }

        $data = [
            'tenant_id'           => $customer->tenant_id,
            'customer_id'           => $customer->id,
            'method'              => $validated['method'],
            'status'              => 'active',
            'is_primary'          => $is_primary,
            'is_default_payout'   => $is_default_payout,
            'account_holder'      => $validated['method'] === 'upi' ? $validated['upi_name'] : $validated['account_holder'] ?? null,
            'bank_name'           => $validated['bank_name'] ?? null,
            'branch_name'         => $validated['branch_name'] ?? null,
            'ifsc_code'           => $validated['ifsc_code'] ?? null,
            'swift_code'          => $validated['swift_code'] ?? null,
            'upi_vpa'             => $validated['upi_id'] ?? null,
            'account_number'      => $account_number,
            'account_number_mask' => $account_number_mask,
            'account_number_hash' => $account_number_hash,
        ];

        // Determine profile_type based on business_id
        if ($business_id) {
            $business = CustomerBusinessProfile::where('customer_id', $customer->id)->where('id', $business_id)->first();
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found for this Customer.'
                ], 422);
            }
            $data['profile_type'] = 'business';
            $data['business_id']  = $business_id;
            $data['business_name'] = $business->trade_name;
        } else {
            $data['profile_type'] = 'individual';
        }

        if ($request->account_id) {
            $account = CustomerPaymentAccount::findOrFail($request->account_id);
            $account->update($data);
            $message = 'Bank/UPI details updated successfully';
        } else {
            $account = CustomerPaymentAccount::create($data);
            $message = 'Bank/UPI details added successfully';
        }

        if ($is_primary == 1) {
            CustomerPaymentAccount::where('customer_id', $customer_id)
                ->where('profile_type', $data['profile_type'])
                ->where('id', '!=', $account->id)
                ->update(['is_primary' => 0]);
        }

        if ($is_default_payout == 1) {
            CustomerPaymentAccount::where('customer_id', $customer_id)
                ->where('profile_type', $data['profile_type'])
                ->where('id', '!=', $account->id)
                ->update(['is_default_payout' => 0]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $account
        ]);
    }

    public function customerBanks($id)
    {
        $accounts = CustomerPaymentAccount::where('customer_id', $id)->where('profile_type', 'individual')->get();
        return response()->json(['data' => $accounts]);
    }

    public function customerBusinessBank($id, $business_id)
    {
        $accounts = CustomerPaymentAccount::where('customer_id', $id)->where('profile_type', 'business')->where('business_id', $business_id)->get();
        return response()->json(['data' => $accounts]);
    }

    public function fetchBank($customer_id, $account_id, $business_id = null)
    {
        $query = CustomerPaymentAccount::where('customer_id', $customer_id)->where('id', $account_id);
        if ($business_id) {
            $query->where('business_id', $business_id)->where('profile_type', 'business');
        } else {
            $query->where('profile_type', 'individual');
        }

        $account = $query->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Bank/UPI account not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $account
        ]);
    }

    public function updateBank(Request $request, $customer_id, $account_id, $business_id = null)
    {
        $customer = Customer::findOrFail($customer_id);

        $rules = ['method' => 'required|in:bank,upi'];

        if ($request->method === 'bank') {
            $rules += [
                'account_holder'    => 'required|string|max:191',
                'bank_name'         => 'required|string|max:191',
                'account_number'    => 'nullable|string|max:50',
                'branch_name'       => 'nullable|string|max:191',
                'ifsc_code'         => 'required|string|max:20',
                'swift_code'        => 'nullable|string|max:50',
                'is_default_payout' => 'nullable|in:0,1',
            ];
        } elseif ($request->method === 'upi') {
            $rules += [
                'upi_name'          => 'required|string|max:191',
                'upi_id'            => 'required|string|max:191',
                'is_default_payout' => 'nullable|in:0,1',
            ];
        }

        $validated = $request->validate($rules);

        $is_primary        = filter_var($request->input('is_primary', 0), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $is_default_payout = filter_var($request->input('is_default_payout', 0), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        $account_number       = null;
        $account_number_mask  = null;
        $account_number_hash  = null;

        if ($validated['method'] === 'bank' && $request->filled('account_number')) {
            $account_number = substr(preg_replace('/\D/', '', $request->input('account_number')), 0, 20);
            $account_number_mask = substr(str_repeat('X', max(0, strlen($account_number) - 4)) . substr($account_number, -4), 0, 20);
            $account_number_hash = hash('sha256', $account_number);
        }

        $data = [
            'tenant_id'           => $customer->tenant_id,
            'customer_id'           => $customer->id,
            'method'              => $validated['method'],
            'status'              => 'active',
            'is_primary'          => $is_primary,
            'is_default_payout'   => $is_default_payout,
            'account_holder'      => $validated['method'] === 'upi' ? $validated['upi_name'] : ($validated['account_holder'] ?? null),
            'bank_name'           => $validated['bank_name'] ?? null,
            'branch_name'         => $validated['branch_name'] ?? null,
            'ifsc_code'           => $validated['ifsc_code'] ?? null,
            'swift_code'          => $validated['swift_code'] ?? null,
            'upi_vpa'             => $validated['upi_id'] ?? null,
        ];

        if ($validated['method'] === 'bank' && $account_number !== null) {
            $data['account_number'] = $account_number;
            $data['account_number_mask'] = $account_number_mask;
            $data['account_number_hash'] = $account_number_hash;
        }

        // Determine profile_type based on business_id
        if ($business_id) {
            $business = CustomerBusinessProfile::where('customer_id', $customer->id)->where('id', $business_id)->first();
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found for this Customer.'
                ], 422);
            }
            $data['profile_type'] = 'business';
            $data['business_id']  = $business_id;
            $data['business_name'] = $business->trade_name;
        } else {
            $data['profile_type'] = 'individual';
        }

        $account = CustomerPaymentAccount::where('customer_id', $customer_id)
            ->where('id', $account_id)
            ->where('profile_type', $data['profile_type'])
            ->firstOrFail();

        $account->update($data);

        if ($is_primary == 1) {
            CustomerPaymentAccount::where('customer_id', $customer_id)
                ->where('profile_type', $data['profile_type'])
                ->where('id', '!=', $account->id)
                ->update(['is_primary' => 0]);
        }

        if ($is_default_payout == 1) {
            CustomerPaymentAccount::where('customer_id', $customer_id)
                ->where('profile_type', $data['profile_type'])
                ->where('id', '!=', $account->id)
                ->update(['is_default_payout' => 0]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bank/UPI details updated successfully',
            'data'    => $account
        ]);
    }

    public function deleteBank($customer_id, $account_id, $business_id = null)
    {
        $query = CustomerPaymentAccount::where('customer_id', $customer_id)->where('id', $account_id);
        if ($business_id) {
            $query->where('business_id', $business_id)->where('profile_type', 'business');
        } else {
            $query->where('profile_type', 'individual');
        }

        $account = $query->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Bank/UPI account not found'
            ], 404);
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank/UPI details deleted successfully'
        ]);
    }

    function permanentBank($customer_id)
    {
        $account = CustomerPaymentAccount::where('customer_id', $customer_id)->where('profile_type', 'individual')->where('is_primary', '1')->first();
        if ($account) {
            return response()->json([
                'success' => true,
                'message' => 'Bank/UPI account fetched successfully',
                'data'    => $account
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Customer bank/UPI account not found',
            ], 404);
        }
    }

    public function permanentBusinessBank($customer_id, $business_id)
    {
        $account = CustomerPaymentAccount::where('customer_id', $customer_id)->where('business_id', $business_id)->where('is_primary', '1')->first();
        if ($account) {
            return response()->json([
                'success' => true,
                'message' => 'Bank/UPI account fetched successfully',
                'data'    => $account
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Customer bank/UPI account not found',
            ], 404);
        }
    }
}
