<?php

namespace App\Http\Controllers\Education;

use App\Models\Course;
use App\Models\CourseClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class CourseClassController extends Controller
{
    public function index()
    {
        return view('Admin.Education.ManageClass.index');
    }

    public function paginate(Request $request)
    {
        $query = CourseClass::with(['sessionYear', 'course'])->orderBy('id', 'desc');

        if ($request->session_year_id) {
            $query->where('session_year_id', $request->session_year_id);
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('session_year', fn($row) => $row->sessionYear->name)
            ->addColumn('class_name', fn($row) => $row->class_name)
            ->addColumn('course_name', fn($row) => $row->course->course_name)
            ->addColumn(
                'is_active',
                fn($row) =>
                $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>'
            )
            ->addColumn(
                'action',
                fn($row) =>
                '<button class="btn btn-sm btn-primary editClass" data-id="' . $row->id . '"> <i class="fas fa-edit"></i> Edit</button>
                 <button class="btn btn-sm btn-danger deleteClass" data-id="' . $row->id . '"><i class="fas fa-trash"></i> Delete</button>'
            )
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_year_id' => 'required|exists:session_years,id',
            'course_id'       => 'required|exists:courses,id',
            'class_name'      => 'required|string|max:255',
            'class_code'      => 'required|string|max:100|unique:course_classes,class_code',
            'description'     => 'nullable|string',
            'is_active'       => 'nullable|boolean'
        ]);

        CourseClass::create([
            'session_year_id' => $request->session_year_id,
            'course_id'       => $request->course_id,
            'class_name'      => $request->class_name,
            'class_code'      => $request->class_code,
            'description'     => $request->description,
            'is_active'       => $request->filled('is_active') ? 1 : 0,
        ]);

        return response()->json(['status' => true, 'message' => 'Class Added Successfully']);
    }

    public function edit($id)
    {
        $class = CourseClass::findOrFail($id);

        // Load courses of that session year for dropdown
        $courses = Course::where('session_year_id', $class->session_year_id)
            ->get(['id', 'course_name']);

        $class->courses = $courses;

        return response()->json($class);
    }

    public function update(Request $request, $id)
    {
        $class = CourseClass::findOrFail($id);

        $request->validate([
            'class_name'      => 'required|string|max:255',
            'class_code'      => 'required|string|max:100|unique:course_classes,class_code,' . $id,
            'description'     => 'nullable|string',
            'is_active'       => 'nullable|boolean'
        ]);

        $class->update([
            'class_name'      => $request->class_name,
            'class_code'      => $request->class_code,
            'description'     => $request->description,
            'is_active'       => $request->filled('is_active') ? 1 : 0,
        ]);

        return response()->json(['status' => true, 'message' => 'Class Updated Successfully']);
    }

    public function destroy($id)
    {
        CourseClass::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Class Deleted Successfully']);
    }

    public function getCoursesBySessionYear(Request $request)
    {
        $sessionYearId = $request->session_year_id;
        $course_id = $request->course_id;
        $courses = CourseClass::where('session_year_id', $sessionYearId)
        ->where('course_id', $course_id)
        ->get();
        return response()->json(['status' => true, 'data' => $courses]);
    }
}
