<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Education\ClassSection;
use Yajra\DataTables\Facades\DataTables;

class ClassSectionController extends Controller
{
    public function index()
    {
        return view('Admin.Education.ManageSections.index');
    }

    public function paginate(Request $request)
    {
        $query = ClassSection::with(['sessionYear', 'course', 'courseClass'])
            ->when($request->session_year_id, fn($q) => $q->where('session_year_id', $request->session_year_id))
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->course_class_id, fn($q) => $q->where('course_class_id', $request->course_class_id));

        /**
         * ✅ Smart sorting support
         * DataTables passes sort_column and sort_dir from frontend
         */
        if ($request->filled('sort_column') && $request->filled('sort_dir')) {
            $validColumns = [
                'session_year' => 'session_year_id',
                'course_name' => 'course_id',
                'class_name' => 'course_class_id',
                'section_name' => 'section_name',
                'section_code' => 'section_code',
            ];

            $sortColumn = $validColumns[$request->sort_column] ?? 'id';
            $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';

            $query->orderBy($sortColumn, $sortDir);
        } else {
            $query->orderByDesc('id');
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('session_year', fn($row) => $row->sessionYear?->name ?? '-')
            ->addColumn('course_name', fn($row) => $row->course?->course_name ?? '-')
            ->addColumn('class_name', fn($row) => $row->courseClass?->class_name ?? '-')
            ->addColumn('section_name', fn($row) => $row->section_name ?? '-')
            ->addColumn('section_code', fn($row) => $row->section_code ?? '-')
            ->addColumn(
                'is_active',
                fn($row) =>
                $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>'
            )
            ->addColumn('action', fn($row) => '
            <button class="btn btn-sm btn-primary editSection" data-id="' . $row->id . '">
                <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn btn-sm btn-danger deleteSection" data-id="' . $row->id . '">
                <i class="fas fa-trash"></i> Delete
            </button>
        ')
            ->rawColumns(['is_active', 'action'])
            ->toJson();
    }



    /**
     * ✅ Store a new section
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_year_id' => 'required|exists:session_years,id',
            'course_id' => 'required|exists:courses,id',
            'course_class_id' => 'required|exists:course_classes,id',
            'section_name' => 'required|string|max:100',
            'section_code' => 'required|string|max:50|unique:class_sections,section_code',
            'is_active' => 'sometimes|boolean',
        ]);

        ClassSection::create([
            'session_year_id' => $request->session_year_id,
            'course_id' => $request->course_id,
            'course_class_id' => $request->course_class_id,
            'section_name' => $request->section_name,
            'section_code' => strtoupper($request->section_code),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json(['status' => true, 'message' => 'Section created successfully']);
    }

    /**
     * ✅ Edit section
     */
    public function edit($id)
    {
        $section = ClassSection::with(['sessionYear', 'course', 'courseClass'])->findOrFail($id);
        return response()->json(['status' => true, 'data' => $section]);
    }

    /**
     * ✅ Update section
     */
    public function update(Request $request, $id)
    {
        $section = ClassSection::findOrFail($id);

        $request->validate([
            'section_name' => 'required|string|max:100',
            'section_code' => 'required|string|max:50|unique:sections,section_code,' . $section->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $section->update([
            'section_name' => $request->section_name,
            'section_code' => strtoupper($request->section_code),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json(['status' => true, 'message' => 'Section updated successfully']);
    }

    /**
     * ✅ Delete section
     */
    public function destroy($id)
    {
        ClassSection::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Section deleted successfully']);
    }
}
