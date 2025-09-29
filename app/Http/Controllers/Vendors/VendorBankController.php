<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VendorPaymentAccount;
use App\Models\VendorBusinessProfile;
use Illuminate\Support\Facades\Crypt;

class VendorBankController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddBankinfo', ['id' => $id]);
    }

    public function saveBank(Request $request, $vendor_id)
{
    $vendor = Vendor::findOrFail($vendor_id);

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
        'tenant_id'           => $vendor->tenant_id,
        'vendor_id'           => $vendor->id,
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

    if ($request->account_id) {
        $account = VendorPaymentAccount::findOrFail($request->account_id);
        $account->update($data);
        $message = 'Bank/UPI details updated successfully';
    } else {
        $account = VendorPaymentAccount::create($data);
        $message = 'Bank/UPI details added successfully';
    }

    if ($is_primary == 1) {
        VendorPaymentAccount::where('vendor_id', $vendor_id)->where('id', '!=', $account->id)->update(['is_primary' => 0]);
    }

    if ($is_default_payout == 1) {
        VendorPaymentAccount::where('vendor_id', $vendor_id)->where('id', '!=', $account->id)->update(['is_default_payout' => 0]);
    }

    return response()->json([
        'success' => true,
        'message' => $message,
        'data'    => $account
    ]);
}

}
