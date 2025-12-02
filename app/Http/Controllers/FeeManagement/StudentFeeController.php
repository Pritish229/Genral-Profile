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

            foreach ($fees as $fee) {

                $feeHead     = (string) $fee['fee_head'];
                $feeType     = (int) $fee['fee_type']; // 0 or 1
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
                    'fee_type'           => (string) $feeType,  // 👈 FIXED
                    'collection_type'    => $collection,

                    'times_in_year'      => $feeTimes,
                    'session_one_name'   => $session,
                    'session_one_amount' => $feeAmount * $feeTimes,
                ]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Fees assigned successfully.'
            ]);
        });
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
