<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VendorPaymentAccount;

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
                'account_holder' => 'required|string|max:191',
                'bank_name'      => 'required|string|max:191',
                'branch_name'    => 'nullable|string|max:191',
                'ifsc_code'      => 'required|string|max:20',
                'swift_code'     => 'nullable|string|max:50',
            ];
        } elseif ($request->method === 'upi') {
            $rules += [
                'upi_name' => 'required|string|max:191',
                'upi_id'   => 'required|string|max:191',
            ];
        }

        $validated = $request->validate($rules);

        $is_primary = $request->is_primary ?? 0;

        if ($request->account_id) {
            $account = VendorPaymentAccount::findOrFail($request->account_id);

            $account->method         = $validated['method'];
            $account->account_holder = $validated['method'] === 'upi'
                ? $validated['upi_name']
                : $validated['account_holder'];
            $account->bank_name      = $validated['bank_name'] ?? null;
            $account->branch_name    = $validated['branch_name'] ?? null;
            $account->ifsc_code      = $validated['ifsc_code'] ?? null;
            $account->swift_code     = $validated['swift_code'] ?? null;
            $account->upi_vpa        = $validated['upi_id'] ?? null;
            $account->is_primary     = $is_primary;

            $account->save();

            $message = 'Bank/UPI details updated successfully';
        } else {
            $account = VendorPaymentAccount::create([
                'tenant_id'      => '1',
                'vendor_id'      => $vendor->id,
                'method'         => $validated['method'],
                'status'         => 'active',
                'is_primary'     => $is_primary,
                'account_holder' => $validated['method'] === 'upi'
                    ? $validated['upi_name']
                    : $validated['account_holder'],
                'bank_name'      => $validated['bank_name'] ?? null,
                'branch_name'    => $validated['branch_name'] ?? null,
                'ifsc_code'      => $validated['ifsc_code'] ?? null,
                'swift_code'     => $validated['swift_code'] ?? null,
                'upi_vpa'        => $validated['upi_id'] ?? null,
            ]);

            $message = 'Bank/UPI details added successfully';
        }

        if ($is_primary == 1) {
            VendorPaymentAccount::where('vendor_id', $vendor_id)
                ->where('id', '!=', $account->id)
                ->update(['is_primary' => 0]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $account
        ]);
    }
}
