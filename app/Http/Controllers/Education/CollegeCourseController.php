<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use App\Models\Education\Course;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Fee\CollegeCourseFee;
use App\Models\Education\CollegeCourse;
use App\Models\Education\UniversityCourse;
use App\Models\Education\UniversityCollege;

class CollegeCourseController extends Controller
{
    public function index($id, $university)
    {
        return view('Admin.Education.CollegeCourse.index', ['college' => $id, 'university' => $university]);
    }

    public function listColleges()
    {
        $colleges = UniversityCollege::select('id', 'org_name')->get();

        return response()->json([
            'status' => true,
            'data' => $colleges
        ]);
    }

    public function store(Request $request)
    {
        $collegeId = $request->college_id;

        $parentId = $request->parent_course_id;
        $parentName = $request->parent_course_name;

        $childId = $request->course_id;
        $childName = $request->course_name;

        $duration = $request->duration_in_years;
        $courseCode = $request->course_code;

        $sessionName = $request->session_name;
        $start = $request->session_start_date;
        $end = $request->session_end_date;

        $fees = $request->fees;

        $parentRow = CollegeCourse::where('college_id', $collegeId)
            ->where('course_id', $parentId)
            ->where('is_parent', 1)
            ->where('session_name', $sessionName)
            ->first();

        if (!$parentRow) {
            $parentRow = CollegeCourseFee::create([
                'college_id'          => $collegeId,

                'parent_course_id'     => $parentId,
                'parent_course_name'   => $parentName,
                'is_parent'            => 1,

                'course_id'            => $parentId,
                'course_name'          => $parentName,
                'course_code'          => $courseCode,
                'duration_in_years'    => $duration,

                'fee_id'               => null,
                'fee_head'             => null,
                'fee_type'             => null,
                'collection_type'      => null,
                'times_in_year'        => null,
                'amount'               => null,
                'total_amount'         => null,

                'session_name'         => $sessionName,
                'session_start_date'   => $start,
                'session_end_date'     => $end,
            ]);
        }

        foreach ($fees as $f) {

            $duplicate = CollegeCourseFee::where('college_id', $collegeId)
                ->where('course_id', $childId)
                ->where('session_name', $sessionName)
                ->where('fee_id', $f['fee_id'])
                ->first();

            if ($duplicate) {
                return response()->json([
                    'status' => false,
                    'message' => $f['fee_head'] . ' already exists for this course & session.'
                ], 409);
            }

            $duplicateByHead = CollegeCourseFee::where('college_id', $collegeId)
                ->where('course_id', $childId)
                ->where('session_name', $sessionName)
                ->where('fee_head', $f['fee_head'])
                ->first();

            if ($duplicateByHead) {
                return response()->json([
                    'status' => false,
                    'message' => $f['fee_head'] . ' already exists in this course & session.'
                ], 409);
            }

            CollegeCourseFee::create([

                'college_id'          => $collegeId,

                'parent_course_id'     => $parentId,
                'parent_course_name'   => $parentName,
                'is_parent'            => 0,

                'course_id'            => $childId,
                'course_name'          => $childName,
                'course_code'          => $courseCode,
                'duration_in_years'    => $duration,

                'fee_id'               => $f['fee_id'],
                'fee_head'             => $f['fee_head'],
                'fee_type'             => 1,
                'collection_type'      => $f['collection_type'],

                'times_in_year'        => $f['times_in_year'],
                'amount'               => $f['amount'],
                'total_amount'         => $f['total_amount'],

                'session_name'         => $sessionName,
                'session_start_date'   => $start,
                'session_end_date'     => $end,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Fee details saved successfully.'
        ]);
    }


    public function datatable(Request $request, $collegeId)
    {
        $query = CollegeCourse::where('college_id', $collegeId)
            ->orderByRaw("CASE WHEN parent_course_id IS NULL THEN 0 ELSE 1 END")
            ->orderBy('parent_course_id')
            ->orderBy('course_name');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn("group", function ($row) {
                return $row->parent_course_id ? 'child' : 'parent';
            })
            ->addColumn("parent_id", function ($row) {
                return $row->parent_course_id;
            })
            ->addColumn("arrow", function ($row) {
                return $row->parent_course_id
                    ? ''
                    : '<i class="fas fa-chevron-right toggle-arrow" data-id="' . $row->id . '" style="cursor:pointer"></i>';
            })
            ->editColumn('course_name', function ($row) {
                $badge = $row->is_parent
                    ? '<span class="badge bg-info ms-2">Parent</span>'
                    : '<span class="badge bg-secondary ms-2">Child</span>';

                return $row->course_name . ' ' . $badge;
            })
            ->editColumn('duration_in_years', function ($row) {
                $y = $row->duration_in_years;
                return $y . ' ' . ($y == 1 ? 'Year' : 'Years');
            })

            /* ------- START DATE COLUMN ------- */
            ->addColumn('starting_date', function ($row) {
                return $row->starting_date
                    ? '<i class="fas fa-calendar-alt me-1 text-primary"></i>' . date('d M Y', strtotime($row->starting_date))
                    : '-';
            })

            /* ------- END DATE COLUMN ------- */
            ->addColumn('ending_date', function ($row) {
                return $row->ending_date
                    ? '<i class="fas fa-calendar-alt me-1 text-primary"></i>' . date('d M Y', strtotime($row->ending_date))
                    : '-';
            })

            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success toggleStatus" data-id="' . $row->id . '">Active</span>'
                    : '<span class="badge bg-danger toggleStatus" data-id="' . $row->id . '">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                <button class="btn btn-primary btn-sm editCourse me-1" data-id="' . $row->id . '">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm deleteCourse" data-id="' . $row->id . '">
                    <i class="fas fa-trash"></i> Delete
                </button>
            ';
            })
            ->rawColumns(['course_name', 'starting_date', 'ending_date', 'is_active', 'arrow', 'action'])
            ->make(true);
    }






    public function show($id)
    {
        $row = CollegeCourse::find($id);

        if (!$row) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $row
        ]);
    }


    public function delete($id)
    {
        $row = CollegeCourse::find($id);

        if (!$row) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ]);
        }

        // If this is a parent → delete all children
        if ($row->is_parent) {
            CollegeCourse::where('parent_course_id', $row->course_id)
                ->where('college_id', $row->college_id)
                ->delete();
        }

        $row->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $row = CollegeCourse::find($id);

        $row->starting_date = $request->starting_date;
        $row->ending_date = $request->ending_date;
        $row->is_active = $request->status;
        $row->save();

        // If parent → update all children
        if ($row->is_parent) {
            CollegeCourse::where('parent_course_id', $row->course_id)
                ->where('college_id', $row->college_id)
                ->update([
                    'starting_date' => $request->starting_date,
                    'ending_date'      => $request->ending_date,
                    'is_active'     => $request->status
                ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Course updated successfully.'
        ]);
    }

    public function parentCourses($college)
    {
        $courses = CollegeCourse::where('college_id', $college)
            ->whereNull('parent_course_id')
            ->orderBy('course_name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $courses
        ]);
    }

    public function childCourses($collegeId, $parentCourseId)
    {
        $courses = CollegeCourse::where('college_id', $collegeId)
            ->where('parent_course_id', $parentCourseId)
            ->orderBy('course_name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $courses
        ]);
    }
}
