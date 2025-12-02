<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use App\Models\Student\Student;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Student\StudentProfile;
use App\Models\Education\CollegeCourse;
use App\Models\Education\StudentCourse;

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

        // 🚫 PREVENT ASSIGNING A STUDENT TWICE IN ANY COURSE
        foreach ($request->students as $student) {
            if (StudentCourse::where('student_id', $student['id'])->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => "{$student['name']} is already enrolled in another course."
                ], 422);
            }
        }

        // CREATE PARENT ROW IF NOT EXISTS
        if ($child->parent_course_id) {

            $parentExists = StudentCourse::where('college_id', $collegeId)
                ->where('course_id', $child->parent_course_id)
                ->where('is_parent', 1)
                ->where('session_year_name', $sessionYear)
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

        // ASSIGN CHILD COURSE STUDENTS
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


    // -------------------------------------------------------------
    // 3️⃣ GET STUDENTS NOT ENROLLED IN ANY COURSE (GLOBAL FILTER)
    // -------------------------------------------------------------
    public function getassignedStudents(Request $request)
    {
        $collegeId = $request->college_id;
        $courseId  = $request->course_id;   // child course
        $session   = $request->session_name;

        if (!$collegeId || !$courseId || !$session) {
            return response()->json([
                "data" => [],
                "message" => "Missing parameters"
            ]);
        }

        $students = StudentCourse::select(
            'students.id',
            'students.student_uid',
            'student_courses.student_name as full_name',
            'students.primary_email',
            'students.primary_phone',
            'students.status'
        )
            ->join('students', 'students.id', '=', 'student_courses.student_id')
            ->where('student_courses.college_id', $collegeId)
            ->where('student_courses.course_id', $courseId)     // child course
            ->where('student_courses.session_year_name', $session)
            ->whereNotNull('student_courses.student_id')
            ->get();

        return response()->json([
            "data" => $students
        ]);
    }
}
