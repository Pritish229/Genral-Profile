<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPaymentAccount;
use App\Models\CustomerBusinessProfile;
use Illuminate\Http\Request;

class CustomerBankController extends Controller
{
    public function index($id)
    {
        return view('Admin.Customers.CustomerProfile.AddBankinfo', compact('id'));
    }

    public function ManageBank($id)
    {
        return view('Admin.Customers.CustomerProfile.ManageBank', compact('id'));
    }

    public function businessBank($id, $business_id)
    {
        return view('Admin.Customers.CustomerProfile.BusinessBank', compact('id', 'business_id'));
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
                'account_type'      => 'required|string|max:50',
                'branch_name'       => 'nullable|string|max:191',
                'ifsc_code'         => 'required|string|max:20',
                'swift_code'        => 'nullable|string|max:50',
            ];
        } elseif ($request->method === 'upi') {
            $rules += [
                'upi_name'          => 'required|string|max:191',
                'upi_id'            => 'required|string|max:191',
            ];
        }

        $validated = $request->validate($rules);

        $is_primary = $request->boolean('is_primary');
        $is_default_payout = $request->boolean('is_default_payout');

        $hasPrimary = CustomerPaymentAccount::where('customer_id', $customer_id)->where('is_primary', 1)->exists();
        if (!$hasPrimary) {
            $is_primary = true;
            $is_default_payout = true;
        }

        $account_number = null;
        $account_number_mask = null;
        $account_number_hash = null;

        if ($validated['method'] === 'bank') {
            $account_number = substr(preg_replace('/\D/', '', $validated['account_number']), 0, 20);
            $account_number_mask = substr(str_repeat('X', max(0, strlen($account_number) - 4)) . substr($account_number, -4), 0, 20);
            $account_number_hash = hash('sha256', $account_number);
        }

        $data = [
            'tenant_id'           => $customer->tenant_id,
            'customer_id'         => $customer->id,
            'method'              => $validated['method'],
            'status'              => 'active',
            'is_primary'          => $is_primary,
            'is_default_payout'   => $is_default_payout,
            'account_holder'      => $validated['method'] === 'upi' ? $validated['upi_name'] : $validated['account_holder'],
            'bank_name'           => $validated['bank_name'] ?? null,
            'branch_name'         => $validated['branch_name'] ?? null,
            'ifsc_code'           => $validated['ifsc_code'] ?? null,
            'swift_code'          => $validated['swift_code'] ?? null,
            'upi_vpa'             => $validated['upi_id'] ?? null,
            'account_number'      => $account_number,
            'account_number_mask' => $account_number_mask,
            'account_number_hash' => $account_number_hash,
            'account_type'        => $validated['method'] === 'bank' ? $validated['account_type'] : null,
        ];

        if ($business_id) {
            $business = CustomerBusinessProfile::where('customer_id', $customer->id)->findOrFail($business_id);
            $data['profile_type'] = 'business';
            $data['business_id'] = $business_id;
            $data['business_name'] = $business->trade_name;
        } else {
            $data['profile_type'] = 'individual';
        }

        if ($request->filled('account_id')) {
            $account = CustomerPaymentAccount::findOrFail($request->account_id);
            $account->update($data);
            $message = 'Bank/UPI details updated successfully';
        } else {
            $account = CustomerPaymentAccount::create($data);
            $message = 'Bank/UPI details added successfully';
        }

        if ($is_primary) {
            CustomerPaymentAccount::where('customer_id', $customer_id)
                ->where('profile_type', $data['profile_type'])
                ->where('id', '!=', $account->id)
                ->update(['is_primary' => 0]);
        }

        if ($is_default_payout) {
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
        $accounts = CustomerPaymentAccount::where('customer_id', $id)
            ->where('profile_type', 'individual')
            ->get();

        return response()->json(['data' => $accounts]);
    }

    public function customerBusinessBank($id, $business_id)
    {
        $accounts = CustomerPaymentAccount::where('customer_id', $id)
            ->where('profile_type', 'business')
            ->where('business_id', $business_id)
            ->get();

        return response()->json(['data' => $accounts]);
    }

    public function fetchBank($customer_id, $business_id, $account_id)
    {
        $query = CustomerPaymentAccount::where('customer_id', $customer_id)
            ->where('id', $account_id);

        if ($business_id && $business_id !== 'null' && $business_id != 0) {
            $query->where('business_id', $business_id)
                ->where('profile_type', 'business');
        } else {
            $query->where('profile_type', 'individual');
        }

        $account = $query->first();

        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Bank/UPI account not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $account]);
    }

    public function updateBank(Request $request, $customer_id, $business_id = null, $account_id)
    {
        $customer = Customer::findOrFail($customer_id);

        $rules = ['method' => 'required|in:bank,upi'];

        if ($request->method === 'bank') {
            $rules += [
                'account_holder' => 'required|string|max:191',
                'bank_name' => 'required|string|max:191',
                'account_number' => 'nullable|string|max:50',
                'account_type' => 'required|string|max:50',
                'branch_name' => 'nullable|string|max:191',
                'ifsc_code' => 'required|string|max:20',
                'swift_code' => 'nullable|string|max:50',
            ];
        } elseif ($request->method === 'upi') {
            $rules += [
                'upi_name' => 'required|string|max:191',
                'upi_id' => 'required|string|max:191',
            ];
        }

        $validated = $request->validate($rules);

        $is_primary = $request->boolean('is_primary');
        $is_default_payout = $request->boolean('is_default_payout');

        $account_number = null;
        $account_number_mask = null;
        $account_number_hash = null;

        if ($validated['method'] === 'bank' && $request->filled('account_number')) {
            $account_number = substr(preg_replace('/\D/', '', $request->input('account_number')), 0, 20);
            $account_number_mask = substr(str_repeat('X', max(0, strlen($account_number) - 4)) . substr($account_number, -4), 0, 20);
            $account_number_hash = hash('sha256', $account_number);
        }

        $data = [
            'tenant_id' => $customer->tenant_id,
            'customer_id' => $customer->id,
            'method' => $validated['method'],
            'status' => 'active',
            'is_primary' => $is_primary,
            'is_default_payout' => $is_default_payout,
            'account_holder' => $validated['method'] === 'upi' ? $validated['upi_name'] : ($validated['account_holder'] ?? null),
            'bank_name' => $validated['bank_name'] ?? null,
            'branch_name' => $validated['branch_name'] ?? null,
            'ifsc_code' => $validated['ifsc_code'] ?? null,
            'swift_code' => $validated['swift_code'] ?? null,
            'upi_vpa' => $validated['upi_id'] ?? null,
            'account_type' => $validated['method'] === 'bank' ? ($validated['account_type'] ?? 'savings') : null,
        ];

        if ($validated['method'] === 'bank' && $account_number) {
            $data['account_number'] = $account_number;
            $data['account_number_mask'] = $account_number_mask;
            $data['account_number_hash'] = $account_number_hash;
        }

        if ($business_id) {
            $business = CustomerBusinessProfile::where('customer_id', $customer->id)->findOrFail($business_id);
            $data['profile_type'] = 'business';
            $data['business_id'] = $business_id;
            $data['business_name'] = $business->trade_name;
        } else {
            $data['profile_type'] = 'individual';
        }

        $account = CustomerPaymentAccount::where('customer_id', $customer_id)->where('id', $account_id)->firstOrFail();
        $account->update($data);

        if ($is_primary) {
            CustomerPaymentAccount::where('customer_id', $customer_id)
                ->where('profile_type', $data['profile_type'])
                ->where('id', '!=', $account->id)
                ->update(['is_primary' => 0]);
        }

        if ($is_default_payout) {
            CustomerPaymentAccount::where('customer_id', $customer_id)
                ->where('profile_type', $data['profile_type'])
                ->where('id', '!=', $account->id)
                ->update(['is_default_payout' => 0]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bank/UPI details updated successfully',
            'data' => $account
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
            return response()->json(['success' => false, 'message' => 'Bank/UPI account not found'], 404);
        }

        $account->delete();

        return response()->json(['success' => true, 'message' => 'Bank/UPI details deleted successfully']);
    }

    public function permanentBank($customer_id)
    {
        $account = CustomerPaymentAccount::where('customer_id', $customer_id)
            ->where('profile_type', 'individual')
            ->where('is_primary', 1)
            ->first();

        return $account
            ? response()->json(['success' => true, 'message' => 'Bank/UPI account fetched successfully', 'data' => $account])
            : response()->json(['success' => false, 'message' => 'Customer bank/UPI account not found'], 404);
    }

    public function permanentBusinessBank($customer_id, $business_id)
    {
        $account = CustomerPaymentAccount::where('customer_id', $customer_id)
            ->where('business_id', $business_id)
            ->where('is_primary', 1)
            ->first();

        return $account
            ? response()->json(['success' => true, 'message' => 'Bank/UPI account fetched successfully', 'data' => $account])
            : response()->json(['success' => false, 'message' => 'Customer bank/UPI account not found'], 404);
    }
}
