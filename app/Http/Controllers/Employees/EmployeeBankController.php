<?php

namespace App\Http\Controllers\Employees;

use App\Models\Employee\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee\EmployeePaymentAccount;

class EmployeeBankController extends Controller
{
    public function index($id)
    {
        return view('Admin.Employees.EmployeeProfile.AddBankinfo', ['id' => $id]);
    }
    public function manageBankForm($id)
    {
        return view('Admin.Employees.EmployeeProfile.ManageBank', ['id' => $id]);
    }

    public function saveBank(Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

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
                'upi_holder_name' => 'required|string|max:191',
                'upi_vpa'         => 'required|string|max:191',
            ];
        }

        if ($request->account_id) {
            $rules['account_id'] = 'required|exists:employee_payment_accounts,id,employee_id,' . $employee_id;
        }

        $rules['is_primary'] = 'sometimes|in:1,0';

        $validated = $request->validate($rules);

        $is_primary = $request->filled('is_primary') ? $validated['is_primary'] : 0;

        $existingCount = EmployeePaymentAccount::where('employee_id', $employee_id)->count();
        $isFirstRecord = ($existingCount === 0);

        if ($request->account_id) {
            $account = EmployeePaymentAccount::findOrFail($request->account_id);

            $is_primary = $request->filled('is_primary') ? $validated['is_primary'] : $account->is_primary;

            $account->method         = $validated['method'];
            $account->account_holder = $validated['method'] === 'upi'
                ? ($validated['upi_holder_name'] ?? $account->account_holder)
                : ($validated['account_holder'] ?? $account->account_holder);
            $account->bank_name      = $validated['bank_name'] ?? $account->bank_name;
            $account->branch_name    = $validated['branch_name'] ?? $account->branch_name;
            $account->ifsc_code      = $validated['ifsc_code'] ?? $account->ifsc_code;
            $account->swift_code     = $validated['swift_code'] ?? $account->swift_code;
            $account->upi_vpa        = $validated['upi_vpa'] ?? $account->upi_vpa;
            $account->is_primary     = $is_primary;

            $account->save();

            $message = 'Bank/UPI details updated successfully';
        } else {
            if ($isFirstRecord) {
                $is_primary = 1;
            } elseif (!$request->filled('is_primary')) {
                $is_primary = 0;
            }

            $account = EmployeePaymentAccount::create([
                'tenant_id'      => $employee->tenant_id,
                'employee_id'    => $employee->id,
                'method'         => $validated['method'],
                'status'         => 'active',
                'is_primary'     => $is_primary,
                'account_holder' => $validated['method'] === 'upi'
                    ? $validated['upi_holder_name']
                    : $validated['account_holder'],
                'bank_name'      => $validated['bank_name'] ?? null,
                'branch_name'    => $validated['branch_name'] ?? null,
                'ifsc_code'      => $validated['ifsc_code'] ?? null,
                'swift_code'     => $validated['swift_code'] ?? null,
                'upi_vpa'        => $validated['upi_vpa'] ?? null,
            ]);

            $message = 'Bank/UPI details added successfully';
        }

        if ($account->is_primary == 1) {
            EmployeePaymentAccount::where('employee_id', $employee_id)
                ->where('id', '!=', $account->id)
                ->update(['is_primary' => 0]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $account
        ]);
    }



    public function manageBank($id)
    {
        $accounts = employeePaymentAccount::where('employee_id', $id)->get();
        return view('Admin.employees.employeeProfile.ManageBankList', [
            'id' => $id,
            'accounts' => $accounts
        ]);
    }

    public function editBank($id, $account_id)
    {
        $account = employeePaymentAccount::findOrFail($account_id);
        return view('Admin.employees.employeeProfile.AddBankinfo', [
            'id' => $id,
            'account' => $account
        ]);
    }

    public function deleteBank($employee_id, $account_id)
    {
        $account = employeePaymentAccount::findOrFail($account_id);

        if ($account->is_primary) {
            return response()->json(['success' => false, 'message' => 'Primary account cannot be deleted']);
        }

        $account->delete();

        return response()->json(['success' => true, 'message' => 'Account deleted successfully']);
    }

    // List Bank
    public function employeeBankList($employee_id)
    {
        $accounts = employeePaymentAccount::where('employee_id', $employee_id)->get();
        return response()->json(['success' => true, 'data' => $accounts]);
    }
}
