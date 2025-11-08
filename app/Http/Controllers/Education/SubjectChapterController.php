<?php

namespace App\Http\Controllers\Education;

use App\Models\Subject;
use App\Models\CourseClass;
use Illuminate\Http\Request;
use App\Models\SubjectChapters;
use App\Http\Controllers\Controller;
use App\Models\SessionYear;

class SubjectChapterController extends Controller
{
    public function index()
    {
        return view('Admin.Education.ManageChapter.index');
    }

    public function paginate(Request $request)
    {
        $query = SubjectChapters::with(['sessionYear', 'course', 'class', 'subject'])
            ->when($request->session_year_id, fn($q) => $q->where('session_year_id', $request->session_year_id))
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->course_class_id, fn($q) => $q->where('course_class_id', $request->course_class_id))
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->orderBy('id', 'desc');

        return datatables()
            ->eloquent($query) // Use eloquent instead of of()
            ->addIndexColumn()
            ->addColumn('session_year', fn($row) => $row->sessionYear?->name ?? '-')
            ->addColumn('course_name', fn($row) => $row->course?->course_name ?? '-')
            ->addColumn('class_name', fn($row) => $row->class?->class_name ?? '-')
            ->addColumn('subject_name', fn($row) => $row->subject?->subject_name ?? '-')
            ->addColumn('chapter_name', fn($row) => $row->chapter_name)
            ->addColumn('action', function ($row) {
                return '
                <button class="btn btn-sm btn-primary editChapter" data-id="' . $row->id . '">
                    <i class="bx bx-edit"></i> Edit 
                </button>
                <button class="btn btn-sm btn-danger deleteChapter" data-id="' . $row->id . '">
                    <i class="bx bx-trash"></i> Delete
                </button>
            ';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_year_id' => 'required|exists:session_years,id',
            'course_id' => 'required|exists:courses,id',
            'course_class_id' => 'required|exists:course_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_name' => 'required|string|max:255',
        ]);

        SubjectChapters::create($request->all());

        return response()->json(['status' => true, 'message' => 'Chapter Added Successfully']);
    }

    public function edit($id)
    {
        $chapter = SubjectChapters::with(['sessionYear', 'course', 'class', 'subject'])
            ->findOrFail($id);

        // No need for extra queries — relationships are already loaded
        return response()->json([
            'id'               => $chapter->id,
            'chapter_name'     => $chapter->chapter_name,
            'session_year_id'  => $chapter->session_year_id,
            'course_id'        => $chapter->course_id,
            'course_class_id'  => $chapter->course_class_id,
            'subject_id'       => $chapter->subject_id,
            'sessionYear'      => [
                'id'   => $chapter->sessionYear?->id,
                'name' => $chapter->sessionYear?->name,
            ],
            'course'           => [
                'id'          => $chapter->course?->id,
                'course_name' => $chapter->course?->course_name,
            ],
            'class'            => [
                'id'         => $chapter->class?->id,
                'class_name' => $chapter->class?->class_name,
            ],
            'subject'          => [
                'id'           => $chapter->subject?->id,
                'subject_name' => $chapter->subject?->subject_name,
            ],

            // Optional: Preload dropdown options (if you want to allow changing class/subject)
            'class_list'       => CourseClass::where('course_id', $chapter->course_id)
                ->select('id', 'class_name')
                ->get(),

            'subject_list'     => Subject::where('course_class_id', $chapter->course_class_id)
                ->select('id', 'subject_name')
                ->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'chapter_name' => 'required|string|max:255',
        ]);

        SubjectChapters::findOrFail($id)->update([
            'chapter_name' => $request->chapter_name
        ]);

        return response()->json(['status' => true, 'message' => 'Chapter Updated Successfully']);
    }

    public function destroy($id)
    {
        SubjectChapters::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Chapter Deleted Successfully']);
    }
}
