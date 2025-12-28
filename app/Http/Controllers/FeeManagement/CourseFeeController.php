<?php

namespace App\Http\Controllers\FeeManagement;

use Carbon\Carbon;
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
            'college_id'         => 'required|integer',
            'course_id'          => 'required|integer',
            'course_code'        => 'required|string',
            'duration_in_years'  => 'required|integer',
            'session_name'       => 'required|string',
            'session_start_date' => 'required|date_format:d-m-Y',
            'session_end_date'   => 'required|date_format:d-m-Y',

            'fees'                       => 'required|array|min:1',
            'fees.*.fee_id'              => 'required|integer',
            'fees.*.fee_head'            => 'required|string',
            'fees.*.collection_type'     => 'required|in:mandatory,optional',
            'fees.*.amount'              => 'required|numeric|min:0',
            'fees.*.times_in_year'       => 'required|integer|min:1',
            'fees.*.total_amount'        => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request): JsonResponse {

            // 🔁 Normalize dates for MySQL
            $sessionStart = Carbon::createFromFormat('d-m-Y', $request->session_start_date)
                ->format('Y-m-d');

            $sessionEnd = Carbon::createFromFormat('d-m-Y', $request->session_end_date)
                ->format('Y-m-d');

            // 🔍 Fetch course
            $course = CollegeCourse::where('college_id', $request->college_id)
                ->where('course_id', $request->course_id)
                ->firstOrFail();

            // 🔍 Fetch parent course
            if (!$course->parent_course_id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Parent course not found.'
                ], 422);
            }

            $parent = CollegeCourse::where('college_id', $request->college_id)
                ->where('course_id', $course->parent_course_id)
                ->firstOrFail();

            // 💾 Save each fee
            foreach ($request->fees as $fee) {

                CollegeCourseFee::updateOrCreate(
                    [
                        'college_id'   => $request->college_id,
                        'course_id'    => $course->course_id,
                        'fee_id'       => $fee['fee_id'],
                        'session_name' => $request->session_name,
                    ],
                    [
                        'course_name'        => $course->course_name,
                        'parent_course_id'   => $parent->course_id,
                        'parent_course_name' => $parent->course_name,
                        'is_parent'          => 0,
                        'course_code'        => $request->course_code,
                        'duration_in_years'  => $request->duration_in_years,
                        'fee_head'           => $fee['fee_head'],
                        'collection_type'    => $fee['collection_type'],
                        'times_in_year'      => $fee['times_in_year'],
                        'amount'             => $fee['amount'],
                        'total_amount'       => $fee['total_amount'],
                        'session_start_date' => $sessionStart,
                        'session_end_date'   => $sessionEnd,
                    ]
                );
            }

            return response()->json([
                'status'  => true,
                'message' => 'Course fees saved successfully.'
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
