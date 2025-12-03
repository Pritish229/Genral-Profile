<?php

namespace App\Http\Controllers\FeeManagement;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Student\Student;
use App\Models\Student\StudentProfile;
use App\Models\Education\CollegeCourse;
use App\Models\Education\StudentCourse;
use App\Models\Fee\CollegeCourseFee;
use App\Models\Fee\StudentCourseFee;
use App\Models\Fee\StudentFeeInstallment;

class StudentFeeController extends Controller
{


    public function index(Request $request)
    {
        $request->validate([
            'student_id'        => 'required|integer',
            'course_id'         => 'required|integer',
            'college_id'        => 'required|integer',
            'session_year_name' => 'required|string',
        ]);

        $student = StudentProfile::where('student_id', $request->student_id)->first();
        if (!$student) {
            $student = Student::findOrFail($request->student_id);
            $student->full_name = $student->student_uid;
        }

        $course = CollegeCourse::where('college_id', $request->college_id)
            ->where('course_id', $request->course_id)
            ->firstOrFail();

        $parent = null;
        if ($course->parent_course_id) {
            $parent = CollegeCourse::where('college_id', $request->college_id)
                ->where('course_id', $course->parent_course_id)
                ->first();
        }

        $fees = CollegeCourseFee::where('college_id', $request->college_id)
            ->where('course_id', $request->course_id)
            ->where('session_name', $request->session_year_name)
            ->get();

        return view('Admin.FeeManagement.StudentFee.index', [
            'student' => $student,
            'course'  => $course,
            'parent'  => $parent,
            'session' => $request->session_year_name,
            'fees'    => $fees,
        ]);
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

            // Load course
            $course = CollegeCourse::where('college_id', $collegeId)
                ->where('course_id', $courseId)
                ->firstOrFail();

            // Parent must exist
            if (!$course->parent_course_id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Parent course does not exist.'
                ], 422);
            }

            // Load parent course
            $parent = CollegeCourse::where('college_id', $collegeId)
                ->where('course_id', $course->parent_course_id)
                ->firstOrFail();

            /**
             * CREATE STUDENT COURSE RECORDS
             */
            // Parent course record
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

            // Child course record
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

            /**
             * CREATE PARENT FEE HEADER (One Empty Row)
             */
            StudentCourseFee::firstOrCreate(
                [
                    'student_id'       => $studentId,
                    'course_id'        => $parent->course_id,   // FIXED
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
                    'duration_in_years'  => $parent->duration_in_years,
                    'student_name'       => $studentName,
                    'fee_id'             => null,
                    'fee_head'           => null,
                    'fee_type'           => null,
                    'collection_type'    => null,
                    'times_in_year'      => null,
                    'session_one_amount' => null,
                ]
            );

            /**
             * CALCULATE TOTAL DUE
             */
            $total = 0;

            foreach ($fees as $fee) {

                $feeHead   = $fee['fee_head'];
                $feeAmount = $fee['amount'];
                $feeTimes  = $fee['times'];

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

                $total += ($feeAmount * $feeTimes);
            }

            /**
             * CREATE INSTALLMENT HEADERS
             */
            // Parent
            $this->createInstallment(
                $studentId,
                $parent->course_id,
                $session,
                $collegeId,
                $parent->course_name,
                $parent->course_code,
                null,
                null,
                1,
                null,
                null,
                $studentName
            );

            // Child
            $this->createInstallment(
                $studentId,
                $course->course_id,
                $session,
                $collegeId,
                $course->course_name,
                $course->course_code,
                $parent->course_id,
                $parent->course_name,
                0,
                $total,
                $total,
                $studentName
            );

            /**
             * INSERT REAL FEES (with correct fee_type)
             */
            foreach ($fees as $fee) {

                $feeHead     = $fee['fee_head'];
                $feeType     = $fee['fee_type'];  // EXACTLY WHAT PAYLOAD SENT
                $feeAmount   = $fee['amount'];
                $feeTimes    = $fee['times'];
                $collection  = $fee['collection_type'];
                $lineAmount  = $feeAmount * $feeTimes;

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
                    'fee_type'           => $feeType,   // SAVED WITHOUT CHANGE
                    'collection_type'    => $collection,
                    'times_in_year'      => $feeTimes,
                    'session_one_name'   => $session,
                    'session_one_amount' => $lineAmount,
                ]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Fees assigned successfully.'
            ]);
        });
    }

    private function createInstallment(
        int $studentId,
        int $courseId,
        string $session,
        int $collegeId,
        string $courseName,
        ?string $courseCode,
        ?int $parentCourseId,
        ?string $parentCourseName,
        int $isParent,
        ?float $totalDue,
        ?float $balanceDue,
        string $studentName
    ): bool {

        try {
            StudentFeeInstallment::updateOrCreate(
                [
                    'student_id'        => $studentId,
                    'course_id'         => $courseId,
                    'session_year_name' => $session,
                    'is_parent'         => $isParent
                ],
                [
                    'college_id'         => $collegeId,
                    'course_name'        => $courseName,
                    'course_code'        => $courseCode,
                    'parent_course_id'   => $parentCourseId,
                    'parent_course_name' => $parentCourseName,
                    'total_due'          => $totalDue,
                    'balance_due'        => $balanceDue,
                    'installment_amount' => null,
                    'installment_no'     => null,
                    'installment_title'  => null,
                    'due_date'           => null,
                    'student_name'       => $studentName
                ]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function viewPage(Request $request)
    {
        $request->validate([
            'student_id'        => 'required|integer',
            'course_id'         => 'required|integer',
            'college_id'        => 'required|integer',
            'session_year_name' => 'required|string',
        ]);

        return view('Admin.FeeManagement.StudentFee.view', [
            'student_id'        => $request->student_id,
            'course_id'         => $request->course_id,
            'college_id'        => $request->college_id,
            'session_year_name' => $request->session_year_name,
        ]);
    }

    public function viewfees(Request $request)
    {
        $request->validate([
            'student_id'        => 'required|integer',
            'course_id'         => 'required|integer',
            'college_id'        => 'required|integer',
            'session_year_name' => 'required|string',
        ]);

        $student = StudentProfile::where('student_id', $request->student_id)->first();
        if (!$student) {
            $student = Student::findOrFail($request->student_id);
            $student->full_name = $student->student_uid;
        }

        $course = CollegeCourse::where('college_id', $request->college_id)
            ->where('course_id', $request->course_id)
            ->firstOrFail();

        $parent = CollegeCourse::where('college_id', $request->college_id)
            ->where('course_id', $course->parent_course_id)
            ->first();

        $fees = StudentCourseFee::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('session_one_name', $request->session_year_name)
            ->orderBy('is_parent', 'desc')
            ->orderBy('id')
            ->get();

        return response()->json([
            'status'  => true,
            'student' => $student,
            'course'  => $course,
            'parent'  => $parent,
            'fees'    => $fees,
            'session' => $request->session_year_name,
        ]);
    }
}
