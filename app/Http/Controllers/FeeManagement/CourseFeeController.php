<?php

namespace App\Http\Controllers\FeeManagement;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Fee\CollegeCourseFee;
use App\Models\Fee\StudentCourseFee;
use App\Models\Education\CollegeCourse;
use App\Models\Education\StudentCourse;
use App\Models\Fee\StudentFeeInstallment;

class CourseFeeController extends Controller
{
    public function index(): View
    {
        return view('Admin.FeeManagement.CourseFees.index');
    }

    public function manage(): View
    {
        return view('Admin.FeeManagement.CourseFees.ManageCourseFee');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'student_id'        => 'required|integer',
            'student_name'      => 'required|string',
            'college_id'        => 'required|integer',
            'course_id'         => 'required|integer',
            'session_year_name' => 'required|string',
            'fees'              => 'required|array|min:1',

            'fees.*.fee_head'        => 'required|string',
            'fees.*.fee_type'        => 'required|in:0,1',
            'fees.*.collection_type' => 'required|in:mandatory,optional',
            'fees.*.amount'          => 'required|numeric|min:0',
            'fees.*.times'           => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request): JsonResponse {

            $studentId   = (int) $request->student_id;
            $studentName = (string) $request->student_name;
            $collegeId   = (int) $request->college_id;
            $courseId    = (int) $request->course_id;
            $session     = (string) $request->session_year_name;
            $fees        = $request->fees;

            $course = CollegeCourse::where('college_id', $collegeId)
                ->where('course_id', $courseId)
                ->firstOrFail();

            if (!$course->parent_course_id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Parent course does not exist.'
                ], 422);
            }

            $parent = CollegeCourse::where('college_id', $collegeId)
                ->where('course_id', $course->parent_course_id)
                ->firstOrFail();

            StudentCourse::firstOrCreate(
                [
                    'student_id'        => $studentId,
                    'course_id'         => $parent->course_id,
                    'session_year_name' => $session,
                ],
                [
                    'college_id'         => $collegeId,
                    'course_name'        => $parent->course_name,
                    'parent_course_id'   => null,
                    'parent_course_name' => null,
                    'is_parent'          => 1,
                    'course_code'        => $parent->course_code,
                    'duration_in_years'  => $parent->duration_in_years,
                    'student_name'       => $studentName,
                    'session_start'      => $course->session_start,
                    'session_end'        => $course->session_end,
                ]
            );

            StudentCourse::firstOrCreate(
                [
                    'student_id'        => $studentId,
                    'course_id'         => $course->course_id,
                    'session_year_name' => $session,
                ],
                [
                    'college_id'         => $collegeId,
                    'course_name'        => $course->course_name,
                    'parent_course_id'   => $parent->course_id,
                    'parent_course_name' => $parent->course_name,
                    'is_parent'          => 0,
                    'course_code'        => $course->course_code,
                    'duration_in_years'  => $course->duration_in_years,
                    'student_name'       => $studentName,
                    'session_start'      => $course->session_start,
                    'session_end'        => $course->session_end,
                ]
            );

            StudentCourseFee::firstOrCreate(
                [
                    'student_id'       => $studentId,
                    'course_id'        => $course->course_id,
                    'session_one_name' => $session,
                    'is_parent'        => 1
                ],
                [
                    'college_id'         => $collegeId,
                    'course_name'        => $parent->course_name,
                    'course_code'        => $parent->course_code,
                    'parent_course_id'   => null,
                    'parent_course_name' => null,
                    'is_parent'          => 1,
                    'duration_in_years'  => $course->duration_in_years,
                    'student_name'       => $studentName,
                    'fee_id'             => null,
                    'fee_head'           => null,
                    'fee_type'           => null,
                    'collection_type'    => null,
                    'times_in_year'      => null,
                    'session_one_amount' => null,
                ]
            );

            $total = 0;

            foreach ($fees as $fee) {

                $feeHead     = (string) $fee['fee_head'];
                $feeType     = (int) $fee['fee_type'];
                $feeAmount   = (float) $fee['amount'];
                $feeTimes    = (int) $fee['times'];
                $collection  = (string) $fee['collection_type'];

                $exists = StudentCourseFee::where('student_id', $studentId)
                    ->where('course_id', $course->course_id)
                    ->where('session_one_name', $session)
                    ->where('fee_head', $feeHead)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status'  => false,
                        'message' => "Fee '{$feeHead}' already exists for this student and session."
                    ], 409);
                }

                $lineAmount = $feeAmount * $feeTimes;
                $total += $lineAmount;

                StudentCourseFee::create([
                    'college_id'         => $collegeId,
                    'course_id'          => $course->course_id,
                    'course_name'        => $course->course_name,
                    'course_code'        => $course->course_code,
                    'parent_course_id'   => $parent->course_id,
                    'parent_course_name' => $parent->course_name,
                    'is_parent'          => 0,
                    'duration_in_years'  => $course->duration_in_years,
                    'student_id'         => $studentId,
                    'student_name'       => $studentName,
                    'fee_id'             => $fee['fee_id'] ?? null,
                    'fee_head'           => $feeHead,
                    'fee_type'           => (string) $feeType,
                    'collection_type'    => $collection,
                    'times_in_year'      => $feeTimes,
                    'session_one_name'   => $session,
                    'session_one_amount' => $lineAmount,
                ]);
            }


            StudentFeeInstallment::updateOrCreate(
                [
                    'student_id'        => $studentId,
                    'course_id'         => $course->course_id,
                    'session_year_name' => $session,
                    'is_parent'         => 1
                ],
                [
                    'college_id'         => $collegeId,
                    'course_name'        => $course->course_name,
                    'course_code'        => $course->course_code,
                    'parent_course_id'   => $parent->course_id,
                    'parent_course_name' => $parent->course_name,
                    'total_due'          => $total,
                    'balance_due'        => $total,
                    'installment_amount' => null,
                    'installment_no'     => null,
                    'installment_title'  => null,
                    'due_date'           => null,
                    'student_name'       => $studentName
                ]
            );

            return response()->json([
                'status'  => true,
                'message' => 'Fees assigned successfully.'
            ]);
        });
    }


    public function list(Request $request): JsonResponse
    {
        $query = CollegeCourseFee::where('is_parent', 0);

        if ($request->filled('college_id')) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->filled('parent_course_id')) {
            $query->where('parent_course_id', $request->parent_course_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('session_name')) {
            $query->where('session_name', $request->session_name);
        }

        $data = $query->orderBy('course_id')
            ->orderBy('fee_head')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function sessions(): JsonResponse
    {
        $sessions = CollegeCourseFee::whereNotNull('session_name')
            ->groupBy('session_name')
            ->orderBy('session_name', 'desc')
            ->pluck('session_name');

        return response()->json([
            'status'   => true,
            'sessions' => $sessions,
        ]);
    }
}
