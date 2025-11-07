<?php

namespace App\Http\Controllers\Education;

use App\Models\Course;
use App\Models\SessionYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    public function index()
    {
        return view('Admin.Education.ManageCourse.index');
    }

    public function paginate(Request $request)
    {
        $query = Course::with('sessionYear')->orderBy('id', 'desc');

        if ($request->session_year_id) {
            $query->where('session_year_id', $request->session_year_id);
        }

        return datatables()->of($query)
            ->addColumn('course_image', function($row){
                $src = $row->course_image ? asset('storage/'.$row->course_image) : asset('no-image.png');
                return '<img src="'.$src.'" width="45" height="45" class="rounded border">';
            })
            ->addColumn('session_year', fn($row) => $row->sessionYear->name)
            ->addColumn('is_active', fn($row) =>
                $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>'
            )
            ->addColumn('action', fn($row) =>
                '<button class="btn btn-sm btn-primary editCourse" data-id="' . $row->id . '">Edit</button>
                 <button class="btn btn-sm btn-danger deleteCourse" data-id="' . $row->id . '">Delete</button>'
            )
            ->rawColumns(['course_image','is_active', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_year_id' => 'required|exists:session_years,id',
            'course_name' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:100',
            'course_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->except('course_image');
        $data['tenet_id'] = 1;
        $data['emp_id'] = 1;
        $data['is_active'] = $request->filled('is_active') ? 1 : 0;

        if ($request->hasFile('course_image')) {
            $data['course_image'] = $request->course_image->store('courses', 'public');
        }

        Course::create($data);

        return response()->json(['status' => true, 'message' => 'Course Added Successfully']);
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $course->course_image_url = $course->course_image ? asset('storage/'.$course->course_image) : null;
        return response()->json($course);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'course_name' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:100',
            'course_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->except('course_image');
        $data['is_active'] = $request->filled('is_active') ? 1 : 0;

        if ($request->hasFile('course_image')) {
            if ($course->course_image && file_exists(public_path('storage/'.$course->course_image))) {
                unlink(public_path('storage/'.$course->course_image));
            }
            $data['course_image'] = $request->course_image->store('courses', 'public');
        }

        $course->update($data);

        return response()->json(['status' => true, 'message' => 'Course Updated Successfully']);
    }

    public function destroy($id)
    {
        Course::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Course Deleted Successfully']);
    }
}
