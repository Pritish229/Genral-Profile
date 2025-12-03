<?php

namespace App\Http\Controllers\FeeManagement;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Student\Student;
use App\Models\Student\StudentProfile;
use App\Models\Education\CollegeCourse;
use App\Models\Fee\StudentFeeInstallment;

class StudentFeeInstallmentController extends Controller
{
    /* ---------------------------------------------------------
     * INSTALLMENT SCHEDULE VIEW
     * --------------------------------------------------------- */
    public function index(Request $request)
    {
        $request->validate([
            'student_id'        => 'required|integer',
            'course_id'         => 'required|integer',
            'session_year_name' => 'required|string',
        ]);

        $student = StudentProfile::where('student_id', $request->student_id)->first();

        if ($student) {
            $student->fullName = trim(
                ($student->first_name ?? '') . ' ' .
                ($student->middle_name ?? '') . ' ' .
                ($student->last_name ?? '')
            );

            if ($student->fullName === '') {
                $student->fullName = $student->student_uid ?? 'Unknown Student';
            }
        } else {
            $s = Student::findOrFail($request->student_id);

            $s->fullName = trim(
                ($s->first_name ?? '') . ' ' .
                ($s->middle_name ?? '') . ' ' .
                ($s->last_name ?? '')
            );

            if ($s->fullName === '') {
                $s->fullName = $s->student_uid ?? 'Unknown Student';
            }

            $student = $s;
        }

        $summary = StudentFeeInstallment::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('session_year_name', $request->session_year_name)
            ->where('is_parent', 0)
            ->whereNull('installment_no')
            ->firstOrFail();

        $course = CollegeCourse::where('college_id', $summary->college_id)
            ->where('course_id', $summary->course_id)
            ->firstOrFail();

        $parent = null;
        if ($summary->parent_course_id) {
            $parent = CollegeCourse::where('college_id', $summary->college_id)
                ->where('course_id', $summary->parent_course_id)
                ->first();
        }

        return view('Admin.FeeManagement.StudentFee.FeeSchedule', [
            'student' => $student,
            'course'  => $course,
            'parent'  => $parent,
            'session' => $request->session_year_name,
            'summary' => $summary
        ]);
    }

    /* ---------------------------------------------------------
     * SUMMARY API FOR DASHBOARD CARDS
     * --------------------------------------------------------- */
    public function summary(Request $request)
    {
        $summary = StudentFeeInstallment::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('session_year_name', $request->session_year_name)
            ->whereNull('installment_no')
            ->first();

        if (!$summary) {
            return response()->json([
                'total_due' => 0,
                'total_paid' => 0,
                'balance_due' => 0,
                'installment_count' => 0
            ]);
        }

        $installments = StudentFeeInstallment::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('session_year_name', $request->session_year_name)
            ->whereNotNull('installment_no')
            ->get();

        $totalPaid = $installments->sum('installment_amount');
        $count = $installments->count();

        return response()->json([
            'total_due'         => number_format($summary->total_due, 2),
            'total_paid'        => number_format($totalPaid, 2),
            'balance_due'       => number_format($summary->balance_due, 2),
            'installment_count' => $count
        ]);
    }

    /* ---------------------------------------------------------
     * LIST API (LEDGER TABLE)
     * --------------------------------------------------------- */
    public function list(Request $request)
    {
        $rows = StudentFeeInstallment::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('session_year_name', $request->session_year_name)
            ->whereNotNull('installment_no')
            ->orderBy('installment_no')
            ->get()
            ->map(function ($r) {

                $overdue = false;
                if ($r->due_date) {
                    if (Carbon::parse($r->due_date)->isPast() && $r->balance_due > 0) {
                        $overdue = true;
                    }
                }

                return [
                    'id'                => $r->id,
                    'installment_no'    => $r->installment_no,
                    'installment_title' => $r->installment_title,
                    'installment_amount'=> number_format($r->installment_amount, 2),
                    'balance_due'       => number_format($r->balance_due, 2),
                    'due_date'          => $r->due_date ? Carbon::parse($r->due_date)->format('d M Y') : '-',
                    'created_at'        => $r->created_at ? $r->created_at->format('d M Y h:i A') : '-',
                    'overdue'           => $overdue
                ];
            });

        return response()->json($rows);
    }

    /* ---------------------------------------------------------
     * CREATE NEW INSTALLMENT
     * --------------------------------------------------------- */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'         => 'required|integer',
            'course_id'          => 'required|integer',
            'session_year_name'  => 'required|string',
            'installment_title'  => 'required|string',
            'installment_amount' => 'required|numeric|min:1',
            'due_date'           => 'required|date'
        ]);

        return DB::transaction(function () use ($request) {

            $summary = StudentFeeInstallment::where('student_id', $request->student_id)
                ->where('course_id', $request->course_id)
                ->where('session_year_name', $request->session_year_name)
                ->whereNull('installment_no')
                ->first();

            if (!$summary) {
                return response()->json(['status' => false, 'message' => 'Summary not found'], 404);
            }

            if ($request->installment_amount > $summary->balance_due) {
                return response()->json([
                    'status' => false,
                    'message' => 'Installment cannot exceed balance due.'
                ], 422);
            }

            $last = StudentFeeInstallment::where('student_id', $request->student_id)
                ->where('course_id', $request->course_id)
                ->where('session_year_name', $request->session_year_name)
                ->whereNotNull('installment_no')
                ->orderBy('installment_no', 'DESC')
                ->first();

            $nextNo = $last ? $last->installment_no + 1 : 1;

            $newBalance = $summary->balance_due - $request->installment_amount;

            StudentFeeInstallment::create([
                'college_id'         => $summary->college_id,
                'course_id'          => $summary->course_id,
                'course_name'        => $summary->course_name,
                'course_code'        => $summary->course_code,
                'parent_course_id'   => $summary->parent_course_id,
                'parent_course_name' => $summary->parent_course_name,
                'is_parent'          => 0,

                'student_id'        => $summary->student_id,
                'student_name'      => $summary->student_name,
                'session_year_name' => $summary->session_year_name,

                'total_due'          => $summary->total_due,
                'balance_due'        => $newBalance,
                'installment_amount' => $request->installment_amount,
                'installment_no'     => $nextNo,
                'installment_title'  => $request->installment_title,
                'due_date'           => $request->due_date
            ]);

            $summary->update(['balance_due' => $newBalance]);

            return response()->json([
                'status' => true,
                'message' => 'Installment created successfully',
                'next_no' => $nextNo
            ]);
        });
    }

    /* ---------------------------------------------------------
     * DELETE INSTALLMENT
     * --------------------------------------------------------- */
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        return DB::transaction(function () use ($request) {

            $row = StudentFeeInstallment::find($request->id);

            if (!$row) {
                return response()->json(['status' => false, 'message' => 'Record not found'], 404);
            }

            if ($row->installment_no === null) {
                return response()->json(['status' => false, 'message' => 'Cannot delete summary record'], 403);
            }

            $summary = StudentFeeInstallment::where('student_id', $row->student_id)
                ->where('course_id', $row->course_id)
                ->where('session_year_name', $row->session_year_name)
                ->whereNull('installment_no')
                ->first();

            $newBalance = $summary->balance_due + $row->installment_amount;

            $summary->update(['balance_due' => $newBalance]);

            $row->delete();

            return response()->json(['status' => true, 'message' => 'Installment deleted']);
        });
    }
}
