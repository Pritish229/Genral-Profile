<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use App\Models\Education\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Education\University;
use Illuminate\Support\Facades\Validator;
use App\Models\Education\UniversityCourse;

class UniversityCourseController extends Controller
{
    public function index($id)
    {
        $university = University::findOrFail($id);

        return view('Admin.Education.ManageUniversity.UniversityCourse', compact('university'));
    }


    // public function viewCourse(University $university, UniversityCourse $course)
    // {
    //     return view('Admin.Education.ManageUniversity.UpdateUnivesityCourse', [
    //         'university' => $university,
    //         'assignedCourse' => $course
    //     ]);
    // }

    public function assignedTreeView(University $university)
    {
        return view('Admin.Education.ManageUniversity.UpdateUnivesityCourse', [
            'university' => $university,
        ]);
    }

    public function store(Request $request, University $university)
    {
        $request->validate([
            'university_id' => 'required|exists:universities,id',
            'course_ids'    => 'required|array|min:1',
            'course_ids.*'  => 'integer|exists:courses,id',
            'is_parent'     => 'required|array',
        ]);

        $courseIds = $request->course_ids ?? [];
        $durations = $request->input('duration', []);
        $semesters = $request->input('semesters', []);
        $actives   = $request->input('is_active', []);
        $isParentInput = $request->input('is_parent', []);

        $courses = Course::with('parent')
            ->whereIn('id', $courseIds)
            ->get()
            ->keyBy('id');

        $existing = UniversityCourse::where('university_id', $university->id)
            ->pluck('course_id')
            ->toArray();

        foreach ($courseIds as $cid) {
            if (in_array($cid, $existing)) {
                return response()->json([
                    'status'     => false,
                    'error_type' => 'duplicate',
                    'message'    => "Course '{$courses[$cid]->course_name}' is already assigned."
                ], 422);
            }
        }

        foreach ($courseIds as $cid) {
            $course = $courses->get($cid);
            if (!$course) continue;

            $isParent = isset($isParentInput[$cid]) && $isParentInput[$cid] == "1"
                ? 'true'
                : 'false';

            $duration = $durations[$cid] ?? null;
            $semester = $semesters[$cid] ?? null;
            $isActive = isset($actives[$cid]) ? 1 : 0;

            if ($isParent === "false") {
                // Validation for child/single courses
                if (!$duration || $duration <= 0) {
                    return response()->json([
                        'status'  => false,
                        'message' => "Duration is required for '{$course->course_name}'."
                    ], 422);
                }

                if (!$semester || $semester <= 0) {
                    $semester = null;
                }
            } else {
                // Parent courses → duration & semester MUST be null
                $duration = null;
                $semester = null;
                $isActive = 1;
            }

            UniversityCourse::create([
                'university_id'     => $university->id,
                'course_id'         => $cid,
                'parent_course_id'  => $course->parent_id,
                'course_name'       => $course->course_name,
                'course_code'       => $course->course_code,
                'parent_name'       => $course->parent?->course_name,
                'duration_in_years' => $duration,
                'total_semesters'   => $semester,
                'is_active'         => $isActive,
                'is_parent'         => $isParent,
            ]);

            // Update parent course to mark it as a parent
            if ($course->parent_id) {
                UniversityCourse::where('university_id', $university->id)
                    ->where('course_id', $course->parent_id)
                    ->update(['is_parent' => 'true']);
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Courses assigned successfully'
        ]);
    }



    public function assignedList(University $university)
    {
        $assigned = UniversityCourse::where('university_id', $university->id)
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('course_id');

        if ($assigned->isEmpty()) {
            return response()->json([
                'status'   => true,
                'assigned' => []
            ]);
        }

        $assignedIds = $assigned->keys()->toArray();

        $parentCourses = Course::with('children')
            ->whereIn('id', $assignedIds)
            ->whereNull('parent_id')
            ->get();

        $result = $parentCourses->map(function ($course) use ($assigned) {

            return $this->buildTreeNode($course, $assigned);
        });

        return response()->json([
            'status'   => true,
            'assigned' => $result->values()
        ]);
    }


    public function delete(Request $request, $universityId, $id)
    {
        // Debugging
        Log::info("Delete Request: university_id={$universityId}, id={$id}");

        $course = UniversityCourse::withTrashed()
            ->where('university_id', $universityId)
            ->where('course_id', $id)
            ->first();

        if (!$course) {
            return response()->json(['status' => false, 'message' => 'Record not found'], 404);
        }


        if ($course->is_parent === "true") {
            UniversityCourse::where('university_id', $course->university_id)->where('parent_course_id', $course->course_id)
                ->delete();
            UniversityCourse::wherewhere('university_id', $course->university_id)->where('course_id', $course->course_id)->delete();
        }

        // Delete the main record
        $course->delete();

        return response()->json([
            'status' => true,
            'message' => 'Course deleted successfully'
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'updates'       => 'required|array',
            'updates.*.duration' => 'nullable|integer|min:1',
            'updates.*.semester' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $universityId = $request->university_id;
        $updates = $request->updates;

        foreach ($updates as $courseId => $data) {
            $record = UniversityCourse::where('course_id', $courseId)
                ->where('university_id', $universityId)
                ->first();

            if (!$record) continue;

            $duration = $data['duration'] ?? null;
            $semester = $data['semester'] ?? null;

            if ($record->is_parent === "false" && (!$duration || $duration <= 0)) {
                return response()->json([
                    'status'  => false,
                    'message' => "Duration required for {$record->course_name}",
                ], 422);
            }

            $record->update([
                'duration_in_years' => $duration,
                'total_semesters'   => $semester ?: null,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Courses updated successfully',
        ]);
    }


    public function updateActive(Request $request)
    {
        $record = UniversityCourse::where('course_id', $request->id)
            ->where('university_id', $request->university_id)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        $isActive = $request->is_active ? 1 : 0;

        // Update the parent course's active status
        $record->update(['is_active' => $isActive]);

        // If the course is a parent and is being deactivated, deactivate all child courses
        if ($record->is_parent === "true" && $isActive === 0) {
            UniversityCourse::where('university_id', $record->university_id)
                ->where('parent_course_id', $record->course_id)
                ->update(['is_active' => 0]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Status updated'
        ]);
    }



    private function buildTreeNode($course, $assigned)
    {
        $record = $assigned[$course->id] ?? null;

        $node = [
            'course_id'   => $course->id,
            'course_name' => $course->course_name,
            'duration'    => $record->duration_in_years,
            'semesters'   => $record->total_semesters,
            'is_active'   => $record->is_active,
            'is_parent'   => $record->is_parent,
            'children'    => []
        ];

        // Children only if assigned
        if ($course->children && $course->children->count()) {

            $children = $course->children->filter(function ($child) use ($assigned) {
                return isset($assigned[$child->id]);
            });

            $node['children'] = $children->map(function ($child) use ($assigned) {

                $rec = $assigned[$child->id];

                return [
                    'course_id'   => $child->id,
                    'course_name' => $child->course_name,
                    'duration'    => $rec->duration_in_years,
                    'semesters'   => $rec->total_semesters,
                    'is_active'   => $rec->is_active,
                    'is_parent'   => 'false',
                ];
            })->values();
        }

        return $node;
    }
}
