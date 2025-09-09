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
    public function storeBank(Request $request, $student_id)
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
                'upi_id'   => 'required|string|max:191',
                'upi_name' => 'required|string|max:191',
            ];
        }

        $validated = $request->validate($rules);

        // Just insert a new record, no checking/updating
        $account = StudentPaymentAccount::create([
            'tenant_id'      => $student->tenant_id,
            'student_id'     => $student->id,
            'method'         => $validated['method'],
            'status'         => 'active',
            'is_primary'     => 1, // you can remove/change this if needed
            'account_holder' => $validated['account_holder'] ?? null,
            'bank_name'      => $validated['bank_name'] ?? null,
            'branch_name'    => $validated['branch_name'] ?? null,
            'ifsc_code'      => $validated['ifsc_code'] ?? null,
            'swift_code'     => $validated['swift_code'] ?? null,
            'upi_id'         => $validated['upi_id'] ?? null,
            'upi_name'       => $validated['upi_name'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $account
        ], 200);
    }
}
