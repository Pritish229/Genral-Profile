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
        $sessionYears = SessionYear::orderBy('name')->get();
        return view('Admin.Education.ManageCourse.index', compact('sessionYears'));
    }

    public function paginate(Request $request)
    {
        $query = Course::with('sessionYear')->orderBy('id', 'desc');

        if ($request->session_year_id) {
            $query->where('session_year_id', $request->session_year_id);
        }

        return datatables()->of($query)
            ->addColumn('session_year', fn($row) => $row->sessionYear->name)

            ->addColumn('is_active', fn($row) => $row->is_active
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('action', fn($row) =>
            '<button class="btn btn-sm btn-primary editCourse" data-id="' . $row->id . '">Edit</button>
             <button class="btn btn-sm btn-danger deleteCourse" data-id="' . $row->id . '">Delete</button>')
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_year_id' => 'required|exists:session_years,id',
            'course_name' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->filled('is_active') ? 1 : 0;

        Course::create($data);
        return response()->json(['status' => true, 'message' => 'Course Added Successfully']);
    }

    public function edit($id)
    {
        return response()->json(Course::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'session_year_id' => 'required|exists:session_years,id',
            'course_name' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->filled('is_active') ? 1 : 0;

        $course->update($data);
        return response()->json(['status' => true, 'message' => 'Course Updated Successfully']);
    }

    public function destroy($id)
    {
        Course::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Course Deleted Successfully']);
    }
}
