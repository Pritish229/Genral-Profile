<?php

namespace App\Http\Controllers\Education;

use App\Models\Course;
use App\Models\Subject;
use App\Models\CourseClass;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class SubjectController extends Controller
{
    public function index()
    {
        return view('Admin.Education.ManageSubject.index');
    }

    public function paginate(Request $request)
    {
        $query = Subject::with(['sessionYear', 'course', 'class'])
            ->when($request->session_year_id, fn($q) => $q->where('session_year_id', $request->session_year_id))
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->course_class_id, fn($q) => $q->where('course_class_id', $request->course_class_id));

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('session_year', fn($row) => $row->sessionYear?->name ?? '-')
            ->addColumn('course_name', fn($row) => $row->course?->course_name ?? '-')
            ->addColumn('class_name', fn($row) => $row->class?->class_name ?? '-')
            ->addColumn('subject_name', fn($row) => $row->subject_name ?? '-')
            ->addColumn('subject_code', fn($row) => $row->subject_code ?? '-')
            ->addColumn(
                'is_active',
                fn($row) => $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>'
            )
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary editSubject" data-id="' . $row->id . '">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger deleteSubject" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i> Delete
                    </button>';
            })
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_year_id'   => 'required|exists:session_years,id',
            'course_id'         => 'required|exists:courses,id',
            'course_class_id'   => 'required|exists:course_classes,id',
            'subject_name'      => 'required|string|max:255',
            'subject_code'      => 'nullable|string|max:100',
            'is_active'         => 'sometimes|boolean',
        ]);

        $data = $request->only(['session_year_id', 'course_id', 'course_class_id', 'subject_name', 'subject_code']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        Subject::create($data);

        return response()->json(['status' => true, 'message' => 'Subject created successfully']);
    }

    public function edit($id)
    {
        $subject = Subject::with(['sessionYear', 'course', 'class'])->findOrFail($id);

        $courses = Course::where('session_year_id', $subject->session_year_id)
            ->where('is_active', 1)
            ->get(['id', 'course_name', 'is_active']);

        $classes = CourseClass::where('session_year_id', $subject->session_year_id)
            ->where('course_id', $subject->course_id)
            ->where('is_active', 1)
            ->get(['id', 'class_name', 'is_active']);

        return response()->json([
            'id'               => $subject->id,
            'session_year_id'  => $subject->session_year_id,
            'sessionYear'      => $subject->sessionYear,
            'course_id'        => $subject->course_id,
            'course_class_id'  => $subject->course_class_id,
            'subject_name'     => $subject->subject_name,
            'subject_code'     => $subject->subject_code,
            'is_active'        => $subject->is_active,
            'courses'          => $courses,
            'classes'          => $classes,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'subject_name'  => 'required|string|max:255',
            'subject_code'  => 'nullable|string|max:100',
            'is_active'     => 'sometimes|boolean',
        ]);

        $subject = Subject::findOrFail($id);
        $data = $request->only(['subject_name', 'subject_code']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $subject->update($data);

        return response()->json(['status' => true, 'message' => 'Subject updated successfully']);
    }

    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Subject deleted successfully']);
    }

    
}
