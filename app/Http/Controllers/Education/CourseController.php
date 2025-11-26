<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use App\Models\Education\Course;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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

            ->addColumn('image', function ($row) {
                $src = $row->course_image ? asset($row->course_image) : asset('images/no-image.png');
                return '<img src="' . $src . '" 
                style="width:50px;height:50px;object-fit:cover;
                border-radius:6px;border:1px solid #ddd;">';
            })

            ->addColumn('type', function ($row) {
                return $row->is_parent
                    ? '<span class="badge bg-primary">Parent</span>'
                    : '<span class="badge bg-info">Child</span>';
            })

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

            ->rawColumns(['image', 'type', 'is_active', 'action'])
            ->make(true);
    }


    public function store(Request $request)
    { {
            $request->validate([
                'parent_id'       => 'nullable|exists:courses,id',
                'course_name'     => 'required|string|max:255',
                'course_code'     => 'nullable|string|max:100|unique:courses,course_code',
                'course_duration' => 'nullable|string|max:100',
                'course_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'description'     => 'nullable|string',
                'is_active'       => 'nullable|boolean',
                'is_parent'       => 'required|in:true,false'
            ]);

            $courseName = $request->course_name;

            // Check if a soft-deleted course exists with same name
            $deletedCourse = Course::onlyTrashed()->where('course_name', $courseName)->first();

            if ($deletedCourse) {
                return response()->json([
                    'status'          => false,
                    'restore_id'      => $deletedCourse->id,
                    'message'         => 'Course already exists but was deleted. Do you want to restore it?',
                    'restore'         => true
                ]);
            }

            // Normal creation
            return $this->createCourse($request);
        }
    }

    // Extracted method to avoid duplication
    private function createCourse($request)
    {
        $data = $request->only([
            'parent_id',
            'course_name',
            'course_code',
            'course_duration',
            'description'
        ]);

        $data['is_parent'] = $request->is_parent === "true" ? 1 : 0;
        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_parent']) {
            $data['parent_id'] = null;
        }

        if ($request->hasFile('course_image')) {
            $filename = time() . '_' . $request->file('course_image')->getClientOriginalName();
            $path = $request->file('course_image')->storeAs('courses', $filename, 'public');
            $data['course_image'] = 'storage/' . $path;
        }

        Course::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Course added successfully'
        ]);
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);

        $preloaded = [];
        if ($course->course_image) {
            $preloaded[] = [
                'id'  => 1,
                'src' => asset($course->course_image)
            ];
        }

        return response()->json([
            'course'     => $course,
            'preloaded'  => $preloaded
        ]);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'parent_id'       => 'nullable|exists:courses,id|not_in:' . $id,
            'course_name'     => 'required|string|max:255',
            'course_code'     => 'nullable|string|max:100|unique:courses,course_code,' . $id,
            'course_duration' => 'nullable|string|max:100',
            'course_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description'     => 'nullable|string',
            'is_active'       => 'nullable|boolean',
            'is_parent'       => 'required|in:true,false'
        ]);

        $data = $request->only([
            'parent_id',
            'course_name',
            'course_code',
            'course_duration',
            'description'
        ]);

        $data['is_parent'] = $request->is_parent === "true" ? 1 : 0;
        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_parent']) {
            $data['parent_id'] = null;
        }

        if ($request->hasFile('course_image')) {

            if ($course->course_image) {
                $oldPath = str_replace('storage/', 'public/', $course->course_image);
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }

            $filename = time() . '_' . $request->file('course_image')->getClientOriginalName();
            $path = $request->file('course_image')->storeAs('courses', $filename, 'public');
            $data['course_image'] = 'storage/' . $path;
        }

        $course->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Course Updated Successfully'
        ]);
    }


    public function restore($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);

        $course->restore();

        // Optional: restore children too if it's a parent
        if (!$course->parent_id || $course->is_parent) {
            Course::onlyTrashed()
                ->where('parent_id', $course->id)
                ->restore();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Course restored successfully!'
        ]);
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $course = Course::findOrFail($id);

            $deletedCount = 1;

            // If this is a PARENT course → delete all its children first
            if (!$course->parent_id || $course->is_parent) {
                $children = Course::where('parent_id', $course->id)->get();

                foreach ($children as $child) {
                    if ($child->course_image) {
                        Storage::disk('public')->delete($child->course_image);
                    }
                    $child->delete();
                }

                $deletedCount += $children->count();
            }

            // Delete the main course image using Storage
            if ($course->course_image) {
                Storage::disk('public')->delete($course->course_image);
            }

            $course->delete();

            return response()->json([
                'status'  => true,
                'message' => $deletedCount > 1
                    ? "Parent course and " . ($deletedCount - 1) . " child course(s) deleted"
                    : "Course deleted successfully"
            ]);
        });
    }

    public function parentCourses()
    {
        return response()->json([
            'status' => true,
            'data'   => Course::where('is_parent', 1)->orderBy('course_name')->get()
        ]);
    }

    public function childCourses($id)
    {
        return response()->json([
            'status' => true,
            'data'   => Course::where('parent_id', $id)->orderBy('course_name')->get()
        ]);
    }

    public function show($id)
    {
        $assigned = UniversityCourse::where('university_id', $id)->pluck('course_id');

        return response()->json([
            'status' => true,
            'university_courses' => $assigned
        ]);
    }
}
