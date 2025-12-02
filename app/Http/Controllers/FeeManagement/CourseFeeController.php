<?php

namespace App\Http\Controllers\FeeManagement;

use Illuminate\Http\Request;
use App\Models\Education\CourseFee;
use App\Models\Education\FeeMaster;
use App\Http\Controllers\Controller;
use App\Models\Education\University;
use App\Models\Fee\CollegeCourseFee;
use App\Models\Education\CollegeCourse;
use App\Models\Education\UniversityCollege;

class CourseFeeController extends Controller
{
    public function index()
    {
        return view('Admin.FeeManagement.CourseFees.index');
    }
    public function manage()
    {
        return view('Admin.FeeManagement.CourseFees.ManageCourseFee');
    }


    public function store(Request $request)
    {
        $collegeId   = $request->college_id;
        $childId     = $request->course_id;
        $sessionName = $request->session_name;

        $start = date('Y-m-d', strtotime($request->session_start_date));
        $end   = date('Y-m-d', strtotime($request->session_end_date));

        $fees = $request->fees;

        $childCourse = CollegeCourse::where('college_id', $collegeId)
            ->where('course_id', $childId)
            ->first();

        if (!$childCourse) {
            return response()->json([
                'status' => false,
                'message' => 'Course not assigned to this college.'
            ], 404);
        }

        $parentId   = $childCourse->parent_course_id;
        $parentName = $childCourse->parent_course_name;

        $childName  = $childCourse->course_name;
        $duration   = $childCourse->duration_in_years;
        $courseCode = $childCourse->course_code;

        if ($parentId) {

            $parentRow = CollegeCourseFee::where('college_id', $collegeId)
                ->where('course_id', $parentId)
                ->where('is_parent', 1)
                ->where('session_name', $sessionName)
                ->first();

            if (!$parentRow) {

                $parentCourse = CollegeCourse::where('college_id', $collegeId)
                    ->where('course_id', $parentId)
                    ->first();

                CollegeCourseFee::create([
                    'college_id'         => $collegeId,
                    'parent_course_id'   => null,
                    'parent_course_name' => null,
                    'is_parent'          => 1,
                    'course_id'          => $parentId,
                    'course_name'        => $parentCourse->course_name,
                    'course_code'        => $parentCourse->course_code,
                    'duration_in_years'  => $parentCourse->duration_in_years,
                    'fee_id'             => null,
                    'fee_head'           => null,
                    'fee_type'           => null,
                    'collection_type'    => null,
                    'times_in_year'      => null,
                    'amount'             => null,
                    'total_amount'       => null,
                    'session_name'       => $sessionName,
                    'session_start_date' => $start,
                    'session_end_date'   => $end
                ]);
            }
        }

        foreach ($fees as $f) {

            $exists = CollegeCourseFee::where('college_id', $collegeId)
                ->where('course_id', $childId)
                ->where('session_name', $sessionName)
                ->where(function ($q) use ($f) {
                    $q->where('fee_id', $f['fee_id'])
                        ->orWhere('fee_head', $f['fee_head']);
                })
                ->first();

            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => $f['fee_head'] . ' already exists for this session.'
                ], 409);
            }

            CollegeCourseFee::create([
                'college_id'         => $collegeId,
                'parent_course_id'   => $parentId,
                'parent_course_name' => $parentName,
                'is_parent'          => 0,
                'course_id'          => $childId,
                'course_name'        => $childName,
                'course_code'        => $courseCode,
                'duration_in_years'  => $duration,
                'fee_id'             => $f['fee_id'],
                'fee_head'           => $f['fee_head'],
                'fee_type'           => $f['fee_type'] ?? 1,
                'collection_type'    => $f['collection_type'] ?? 'mandatory',
                'times_in_year'      => $f['times_in_year'],
                'amount'             => $f['amount'],
                'total_amount'       => $f['total_amount'],
                'session_name'       => $sessionName,
                'session_start_date' => $start,
                'session_end_date'   => $end
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Fee details saved successfully.'
        ]);
    }


    public function list(Request $request)
    {
        $query = CollegeCourseFee::where('is_parent', 0);

        if ($request->college_id) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->parent_course_id) {
            $query->where('parent_course_id', $request->parent_course_id);
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->session_name) {
            $query->where('session_name', $request->session_name);
        }

        $data = $query->orderBy('course_id')->orderBy('fee_head')->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
    public function sessions()
    {
        $sessions = CollegeCourseFee::whereNotNull('session_name')->groupBy('session_name')->pluck('session_name');

        return response()->json($sessions);
    }
}
