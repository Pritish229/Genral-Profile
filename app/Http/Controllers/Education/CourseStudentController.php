<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use App\Models\Student\Student;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Fee\StudentCourseFee;
use App\Models\Student\StudentProfile;
use App\Models\Education\CollegeCourse;
use App\Models\Education\StudentCourse;
use App\Models\Fee\StudentFeeInstallment;

class CourseStudentController extends Controller
{
    public function index()
    {
        return view('Admin.Education.CollegeCourse.AddStudent');
    }

    public function studentassigned()
    {
        return view('Admin.Education.CollegeCourse.StudentAssignedCourse');
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $collegeId   = $request->college_id;
            $childId     = $request->course_id;
            $sessionYear = $request->session_year_name;

            $child = CollegeCourse::where('college_id', $collegeId)
                ->where('course_id', $childId)
                ->first();

            if (!$child) {
                return response()->json([
                    'status' => false,
                    'message' => 'Course not assigned to this college.'
                ], 404);
            }

            foreach ($request->students as $student) {
                if (StudentCourse::where('student_id', $student['id'])->exists()) {
                    return response()->json([
                        'status' => false,
                        'message' => "{$student['name']} is already enrolled in another course."
                    ], 422);
                }
            }

            if ($child->parent_course_id) {
                $parentExists = StudentCourse::where('college_id', $collegeId)
                    ->where('course_id', $child->parent_course_id)
                    ->where('is_parent', 1)
                    ->where('session_year_name', $sessionYear)
                    ->lockForUpdate()
                    ->exists();

                if (!$parentExists) {
                    $parent = CollegeCourse::where('college_id', $collegeId)
                        ->where('course_id', $child->parent_course_id)
                        ->first();

                    StudentCourse::create([
                        'college_id'         => $collegeId,
                        'course_id'          => $parent->course_id,
                        'course_name'        => $parent->course_name,
                        'is_parent'          => 1,
                        'student_id'         => null,
                        'student_name'       => null,
                        'session_year_name'  => $sessionYear,
                        'session_start'      => $request->session_start,
                        'session_end'        => $request->session_end
                    ]);
                }
            }

            foreach ($request->students as $student) {
                StudentCourse::create([
                    'college_id'         => $collegeId,
                    'course_id'          => $child->course_id,
                    'course_name'        => $child->course_name,
                    'parent_course_id'   => $child->parent_course_id,
                    'parent_course_name' => $child->parent_course_name,
                    'is_parent'          => 0,
                    'student_id'         => $student['id'],
                    'student_name'       => $student['name'],
                    'session_year_name'  => $sessionYear,
                    'session_start'      => $request->session_start,
                    'session_end'        => $request->session_end
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Course Assigned to Selected Students'
            ]);
        });
    }



    public function getnewStudents(Request $request)
    {
        // Students already assigned in ANY course
        $assignedIds = StudentCourse::whereNotNull('student_id')
            ->pluck('student_id')
            ->toArray();

        // Fetch only NEW (never assigned) students
        $students = Student::select(
            'students.id',
            'students.student_uid',
            DB::raw("CONCAT(student_profiles.first_name, ' ', student_profiles.last_name) AS full_name"),
            'students.primary_email',
            'students.primary_phone',
            'students.status'
        )
            ->leftJoin('student_profiles', 'student_profiles.student_id', '=', 'students.id')
            ->whereNotIn('students.id', $assignedIds)
            ->get();

        return response()->json([
            "data" => $students
        ]);
    }



    public function getYearwiseDataTable(Request $request)
    {
        $collegeId = $request->college_id;
        $courseId  = $request->course_id;
        $session   = $request->session_name;

        $query = StudentCourse::select(
            'students.id',
            'students.student_uid',
            'student_courses.student_name as full_name',
            'students.primary_email',
            'students.primary_phone',
            'students.status'
        )
            ->join('students', 'students.id', '=', 'student_courses.student_id')
            ->where('student_courses.college_id', $collegeId)
            ->where('student_courses.course_id', $courseId)
            ->where('student_courses.session_year_name', $session)
            ->whereNotNull('student_courses.student_id');

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($courseId, $collegeId, $session) {

                // Check: Has assigned fee?
                $alreadyAssigned = StudentCourseFee::where('student_id', $row->id)
                    ->where('course_id', $courseId)
                    ->where('session_one_name', $session)
                    ->exists();

                // Check: Has installment summary?
                $installmentExists = StudentFeeInstallment::where('student_id', $row->id)
                    ->where('course_id', $courseId)
                    ->where('session_year_name', $session)
                    ->exists();

                // URLs
                $assignUrl = route("fee.studentfee.index", [
                    "student_id"        => $row->id,
                    "course_id"         => $courseId,
                    "college_id"        => $collegeId,
                    "session_year_name" => $session
                ]);

                $viewUrl = route("fee.studentfee.view", [
                    "student_id"        => $row->id,
                    "course_id"         => $courseId,
                    "college_id"        => $collegeId,
                    "session_year_name" => $session
                ]);

                $scheduleUrl = route("fee.FeeSchdule.index", [
                    "student_id"        => $row->id,
                    "course_id"         => $courseId,
                    "session_year_name" => $session
                ]);

                $paymentUrl = route("fee.payment.index", [
                    "student_id"        => $row->id,
                    "course_id"         => $courseId,
                    "session_year_name" => $session
                ]);

                $btns = '';

                // 1️⃣ Assign Fee Button (show only if not assigned)
                if (!$alreadyAssigned) {
                    $btns .= '<a href="' . $assignUrl . '" class="btn btn-sm btn-primary me-1">Assign Fee</a>';
                }

                // 2️⃣ View Fee Button (always show)
                $btns .= '<a href="' . $viewUrl . '" class="btn btn-sm btn-success me-1">View Fee</a>';

                // 3️⃣ Payment Schedule Button (show only if installments exist)
                if ($installmentExists) {
                    $btns .= '<a href="' . $scheduleUrl . '" class="btn btn-sm btn-warning me-1">Payment Schedule</a>';
                } else {
                    // If Assign Fee button is not rendered, show after View Fee
                    if ($alreadyAssigned) {
                        $btns .= '<a href="' . $scheduleUrl . '" class="btn btn-sm btn-warning me-1">Payment Schedule</a>';
                    }
                }

                // 4️⃣ Payment Button (show only if installments exist)
                if ($installmentExists) {
                    $btns .= '<a href="' . $paymentUrl . '" class="btn btn-sm btn-info">Payment</a>';
                } else {
                    if ($alreadyAssigned) {
                        $btns .= '<a href="' . $paymentUrl . '" class="btn btn-sm btn-info">Payment</a>';
                    }
                }

                return $btns;
            })

            ->rawColumns(['action'])
            ->make(true);
    }
}
