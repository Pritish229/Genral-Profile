<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use App\Models\Education\Course;
use App\Http\Controllers\Controller;
use App\Models\Education\University;
use App\Models\Education\UniversityCourse;

class CourseController extends Controller
{
    public function index()
    {
        return view('Admin.Education.ManageCourse.index');
    }

    public function paginate(Request $request)
    {
        $query = Course::with('parent')->orderBy('id', 'desc');

        return datatables()->of($query)
            ->addColumn('parent', function ($row) {
                return $row->parent ? $row->parent->course_name : '—';
            })
            ->addColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-sm btn-primary editCourse" data-id="' . $row->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteCourse" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'parent_id'   => 'nullable|exists:courses,id',
            'course_name' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:100|unique:courses,course_code',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
            'is_parent'   => 'required|in:true,false'
        ]);

        $data = $request->only([
            'parent_id',
            'course_name',
            'course_code',
            'description',
            'is_parent'
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->is_parent === "true") {
            $data['parent_id'] = null;
        }

        Course::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Course Added Successfully'
        ]);
    }

    public function edit($id)
    {
        $course = Course::with('parent')->findOrFail($id);
        return response()->json($course);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'parent_id'   => 'nullable|exists:courses,id|not_in:' . $id,
            'course_name' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:100|unique:courses,course_code,' . $id,
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
            'is_parent'   => 'required|in:true,false'
        ]);

        $data = $request->only([
            'parent_id',
            'course_name',
            'course_code',
            'description',
            'is_parent'
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->is_parent === "true") {
            $data['parent_id'] = null;
        }

        $course->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Course Updated Successfully'
        ]);
    }

    public function destroy($id)
    {
        Course::findOrFail($id)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Course Deleted Successfully'
        ]);
    }

    public function listAll()
    {
        return response()->json([
            'status' => true,
            'data'   => Course::orderBy('course_name')->get()
        ]);
    }

    public function parentCourses()
    {
        return response()->json([
            'status' => true,
            'data'   => Course::where('is_parent', true)->get()
        ]);
    }

    public function show($id)
    {
        $university = University::findOrFail($id);

        $assigned = UniversityCourse::where('university_id', $id)
            ->get(['course_id']);

        return response()->json([
            'status' => true,
            'university_courses' => $assigned
        ]);
    }
}
