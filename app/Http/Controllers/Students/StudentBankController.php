<?php

namespace App\Http\Controllers\Students;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StudentPaymentAccount;

class StudentBankController extends Controller
{
    public function index($id)
    {
        return view('Admin.Students.StudentProfile.AddBankinfo', ['id' => $id]);
    }
    public function manageBankForm($id)
    {
        return view('Admin.Students.StudentProfile.ManageBank', ['id' => $id]);
    }

    // Unified create/update
    public function saveBank(Request $request, $student_id)
    {
        $student = Student::findOrFail($student_id);

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

        $validated = $request->validate($rules);

        // Determine is_primary
        $existingAccounts = StudentPaymentAccount::where('student_id', $student_id)->count();
        $is_primary = $existingAccounts == 0 ? 1 : 0;

        if ($request->account_id) {
            // Update existing account
            $account = StudentPaymentAccount::findOrFail($request->account_id);
            $account->method         = $validated['method'];
            $account->account_holder = $validated['method'] === 'upi'
                ? $validated['upi_holder_name']
                : $validated['account_holder'];
            $account->bank_name      = $validated['bank_name'] ?? null;
            $account->branch_name    = $validated['branch_name'] ?? null;
            $account->ifsc_code      = $validated['ifsc_code'] ?? null;
            $account->swift_code     = $validated['swift_code'] ?? null;
            $account->upi_vpa        = $validated['upi_vpa'] ?? null;
            // Do NOT change is_primary on update
            $account->save();

            $message = 'Bank/UPI details updated successfully';
        } else {
            // Create new account
            $account = StudentPaymentAccount::create([
                'tenant_id'      => $student->tenant_id,
                'student_id'     => $student->id,
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

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $account
        ]);
    }





    public function manageBank($id)
    {
        $accounts = StudentPaymentAccount::where('student_id', $id)->get();
        return view('Admin.Students.StudentProfile.ManageBankList', [
            'id' => $id,
            'accounts' => $accounts
        ]);
    }

    public function editBank($id, $account_id)
    {
        $account = StudentPaymentAccount::findOrFail($account_id);
        return view('Admin.Students.StudentProfile.AddBankinfo', [
            'id' => $id,
            'account' => $account
        ]);
    }

    public function deleteBank($student_id, $account_id)
    {
        $account = StudentPaymentAccount::findOrFail($account_id);

        if ($account->is_primary) {
            return response()->json(['success' => false, 'message' => 'Primary account cannot be deleted']);
        }

        $account->delete();

        return response()->json(['success' => true, 'message' => 'Account deleted successfully']);
    }

    // List Bank
    public function studentBankList($student_id)
    {
        $accounts = StudentPaymentAccount::where('student_id', $student_id)->get();
        return response()->json(['success' => true, 'data' => $accounts]);
    }
}
