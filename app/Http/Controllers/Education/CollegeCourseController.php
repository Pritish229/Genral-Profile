<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use App\Models\Education\CollegeCourse;
use App\Models\Education\UniversityCollege;
use App\Models\Education\Course;
use App\Models\Education\UniversityCourse;
use Illuminate\Http\Request;

class CollegeCourseController extends Controller
{
    public function index($id , $university)
    {
        return view('Admin.Education.CollegeCourse.index', ['college' => $id , 'university'=> $university]);
    }

    public function listColleges()
    {
        $colleges = UniversityCollege::select('id', 'org_name')->get();

        return response()->json([
            'status' => true,
            'data' => $colleges
        ]);
    }

    public function getCollegeCourses($collegeId)
    {
        $college = UniversityCollege::with('university')->findOrFail($collegeId);
        $universityId = $college->university_id;

        // Get ALL assigned course IDs for this college (both parent programs and year-wise)
        $assignedCourseIds = CollegeCourse::where('college_id', $collegeId)
            ->pluck('course_id')
            ->toArray();

        // Also get parent_course_id mappings for year-wise assignments
        $assignedYearMappings = CollegeCourse::where('college_id', $collegeId)
            ->whereNotNull('parent_course_id')
            ->get()
            ->keyBy('course_id'); // key = year course ID → has parent_course_id

        // Load root courses (parent programs) for this university + their children
        $courses = Course::whereHas('universities', fn($q) => $q->where('universities.id', $universityId))
            ->whereNull('parent_id')
            ->with('children')
            ->get()
            ->map(function ($parentCourse) use ($assignedCourseIds, $assignedYearMappings) {

                // Is the PARENT program itself assigned? (i.e. whole MCA assigned, not just years)
                $parentIsAssigned = in_array($parentCourse->id, $assignedCourseIds);

                // Attach flag to parent
                $parentCourse->is_assigned = $parentIsAssigned;

                // Process children (year-wise courses like 1st Year, 2nd Year)
                $children = $parentCourse->children->map(function ($child) use ($assignedCourseIds, $assignedYearMappings, $parentCourse) {

                    // Is this specific year assigned to this college?
                    $isAssigned = in_array($child->id, $assignedCourseIds);

                    // Optional: attach parent info for frontend clarity
                    $child->parent_course_id = $parentCourse->id;
                    $child->is_assigned = $isAssigned;

                    return $child;
                });

                // Replace original children with modified ones (with is_assigned flag)
                $parentCourse->children = $children;

                return $parentCourse;
            });

        return response()->json([
            'status' => true,
            'data'   => $courses
        ]);
    }


    public function assignCourses(Request $request, $collegeId)
    {
        $college = UniversityCollege::findOrFail($collegeId);

        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id'
        ]);

        $now = now();
        $universityId = $college->university_id;

        // Get all current parent course IDs for this college
        $existingParentIds = CollegeCourse::where('college_id', $collegeId)
            ->where('is_parent', 1)
            ->pluck('course_id')
            ->toArray();

        // Track which parent courses we need to create
        $parentsToCreate = [];

        foreach ($request->course_ids as $courseId) {
            $course = Course::find($courseId);

            // If it's a child course (has parent_id) → we need its parent
            if ($course->parent_id) {
                $parentCourseId = $course->parent_id;
                $parentCourse = Course::find($parentCourseId);

                // If parent doesn't exist yet → mark it for creation
                if (!in_array($parentCourseId, $existingParentIds) && !in_array($parentCourseId, $parentsToCreate)) {
                    $parentsToCreate[] = $parentCourseId;

                    // Create parent entry
                    CollegeCourse::create([
                        'university_id'      => $universityId,
                        'college_id'         => $collegeId,
                        'course_id'          => $parentCourse->id,
                        'course_name'        => $parentCourse->course_name,
                        'course_code'        => $parentCourse->course_code,
                        'parent_course_id'   => null,
                        'duration_in_years'  => $parentCourse->course_duration,
                        'starting_time'      => $now,
                        'is_parent'          => 1,
                        'is_active'          => 1,
                    ]);

                    $existingParentIds[] = $parentCourseId; // mark as now exists
                }

                CollegeCourse::create([
                    'university_id'      => $universityId,
                    'college_id'         => $collegeId,
                    'course_id'          => $course->id,
                    'course_name'        => $parentCourse->course_name . " - " . $course->course_name,
                    'course_code'        => $parentCourse->course_code,
                    'parent_course_id'   => $parentCourse->id,
                    'parent_course_name' => $parentCourse->course_name,
                    'duration_in_years'  => 1,
                    'starting_time'      => $now->copy()->addYears($course->course_name === '1st Year' ? 0 : 1),
                    'is_parent'          => 0,
                    'is_active'          => 1,
                ]);
            }
            else {
                if (!in_array($course->id, $existingParentIds)) {
                    CollegeCourse::create([
                        'university_id'      => $universityId,
                        'college_id'         => $collegeId,
                        'course_id'          => $course->id,
                        'course_name'        => $course->course_name,
                        'course_code'        => $course->course_code,
                        'parent_course_id'   => null,
                        'duration_in_years'  => $course->course_duration,
                        'starting_time'      => $now,
                        'is_parent'          => 1,
                        'is_active'          => 1,
                    ]);

                    $existingParentIds[] = $course->id;
                }

                if ($course->course_duration > 1) {
                    foreach ($course->children as $child) {
                        CollegeCourse::updateOrCreate(
                            [
                                'college_id'       => $collegeId,
                                'course_id'        => $child->id,
                                'parent_course_id' => $course->id,
                            ],
                            [
                                'university_id'      => $universityId,
                                'course_name'        => $course->course_name . " - " . $child->course_name,
                                'course_code'        => $course->course_code,
                                'duration_in_years'  => 1,
                                'starting_time'      => $now->copy()->addYears($child->course_name === '1st Year' ? 0 : 1),
                                'is_parent'          => 0,
                                'is_active'          => 1,
                            ]
                        );
                    }
                }
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Courses assigned successfully!'
        ]);
    }
}
