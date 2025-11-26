<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use App\Models\Education\Course;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Education\University;
use App\Models\Education\UniversityCourse;

class UniversityCourseController extends Controller
{
    public function index($id)
    {
        $university = University::findOrFail($id);

        return view('Admin.Education.ManageUniversity.UniversityCourse', [
            'university' => $university,
            'university_id' => $university->id
        ]);
    }

    public function paginate(Request $request)
    {
        $universityId = $request->input('university_id'); // This will now work

        $query = UniversityCourse::where('university_id', $universityId)
            ->orderBy('id', 'asc');

        return datatables()->of($query)
            ->addColumn('parent', fn($row) => $row->parent_course_name ?: '—')
            ->addColumn('course_code', fn($row) => $row->course_code)
            ->addColumn('duration', fn($row) => $row->duration_in_years ? $row->duration_in_years . ' Years' : '—')
            ->addColumn('type', fn($row) => $row->is_parent ? '<span class="badge bg-primary">Parent</span>' : '<span class="badge bg-info">Child</span>')
            ->addColumn('status', fn($row) => $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('action', fn($row) => '<button class="btn btn-sm btn-danger deleteUC" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>')
            ->rawColumns(['type', 'status', 'action'])
            ->make(true);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'university_id'      => 'required|exists:universities,id',
            'parent_course_id'   => 'required|exists:courses,id',
            'child_course_ids'   => 'required|array'
        ]);

        $universityId = $request->university_id;
        $parentId     = $request->parent_course_id;
        $childIds     = $request->child_course_ids;

        $parent = Course::findOrFail($parentId);

        $existing = UniversityCourse::where('university_id', $universityId)
            ->pluck('course_id')
            ->toArray();

        if (!in_array($parentId, $existing)) {
            UniversityCourse::create([
                'university_id'        => $universityId,
                'course_id'            => $parentId,
                'parent_course_id'     => null,
                'course_name'          => $parent->course_name,
                'course_code'          => $parent->course_code,
                'duration_in_years'    => $parent->course_duration,
                'parent_course_name'   => null,
                'is_parent'            => 1,
                'is_active'            => 1
            ]);
        }

        foreach ($childIds as $cid) {
            if (in_array($cid, $existing)) {
                continue;
            }

            $child = Course::find($cid);
            if (!$child) {
                continue;
            }

            UniversityCourse::create([
                'university_id'        => $universityId,
                'course_id'            => $cid,
                'parent_course_id'     => $parentId,
                'course_name'          => $child->course_name,
                'course_code'          => $child->course_code,
                'duration_in_years'    => $child->course_duration,
                'parent_course_name'   => $parent->course_name,
                'is_parent'            => 0,
                'is_active'            => 1
            ]);
        }

        return response()->json(['status' => true]);
    }


    protected function attachCourse(University $university, Course $course, ?Course $parent = null, bool $asParent = false)
    {
        UniversityCourse::updateOrCreate(
            [
                'university_id' => $university->id,
                'course_id'     => $course->id,
            ],
            [
                'course_name'         => $course->course_name,
                'course_code'         => $course->course_code,
                'parent_course_id'    => $parent ? $parent->id : null,
                'parent_course_name'  => $parent ? $parent->course_name : null,
                'duration_in_years'   => $course->course_duration,
                'is_parent'           => $asParent ? 1 : 0,
                'is_active'           => 1,
            ]
        );
    }

    public function edit($id)
    {
        $row = UniversityCourse::findOrFail($id);

        return response()->json($row);
    }

    public function update(Request $request, $id)
    {
        $row = UniversityCourse::findOrFail($id);

        $request->validate([
            'course_name'       => 'nullable|string|max:255',
            'course_code'       => 'nullable|string|max:100',
            'duration_in_years' => 'nullable|integer|min:1',
            'is_active'         => 'nullable|boolean',
        ]);

        $data = $request->only([
            'course_name',
            'course_code',
            'duration_in_years',
        ]);

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $row->update($data);

        return response()->json(['status' => true]);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $uc = UniversityCourse::findOrFail($id);

            if (!$uc->parent_course_id) { // it's a parent
                UniversityCourse::where('university_id', $uc->university_id)
                    ->where('parent_course_id', $uc->course_id)
                    ->delete();
            }

            $uc->delete();
        });

        return response()->json(['status' => true]);
    }

    public function restore(Request $request, $universityId, $id)
    {
        $course = UniversityCourse::withTrashed()
            ->where('university_id', $universityId)
            ->where('course_id', $id)
            ->first();

        if (!$course) {
            return response()->json(['status' => false, 'message' => 'Record not found'], 404);
        }

        if (!$course->trashed()) {
            return response()->json(['status' => false, 'message' => 'Record is not deleted'], 400);
        }

        // Restore the main record
        $course->restore();

        // If parent course was deleted, restore all child courses
        if ($course->is_parent === "true") {
            UniversityCourse::withTrashed()
                ->where('university_id', $course->university_id)
                ->where('parent_course_id', $course->course_id)
                ->restore();
        }

        return response()->json([
            'status' => true,
            'message' => 'Course restored successfully'
        ]);
    }

    public function getDeletedCourses(University $university)
    {
        $deleted = UniversityCourse::onlyTrashed()
            ->where('university_id', $university->id)
            ->get();

        return response()->json([
            'status' => true,
            'deleted' => $deleted
        ]);
    }

    public function getUniversityCourses($universityId)
    {

        $courses = Course::whereHas('universities', function ($q) use ($universityId) {
            $q->where('universities.id', $universityId);
        })
            ->with('children')
            ->whereNull('parent_id')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $courses
        ]);
    }

    public function childCourses($universityId, $course_id)
    {
        $courses = Course::whereHas('universities', function ($q) use ($universityId) {
            $q->where('universities.id', $universityId);
        })
            ->with('children')
            ->where('parent_id', $course_id)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $courses
        ]);
    }
}
