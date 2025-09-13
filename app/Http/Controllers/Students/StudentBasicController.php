<?php

namespace App\Http\Controllers\Students;

use Carbon\Carbon;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentProfile;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class StudentBasicController extends Controller
{
    public function index($id)
    {
        return view('Admin.Students.StudentProfile.Basicinfo', ['id' => $id]);
    }

    public function studentlist()
    {
        return view('Admin.Students.StudentProfile.StudentList',);
    }
    public function studentDetails($student_id)
    {
        return view('Admin.Students.StudentProfile.StudentDetails', [
            'id' => $student_id
        ]);
    }
    public function manageDetail($student_id)
    {
        return view('Admin.Students.StudentProfile.ManageBasicinfo', [
            'id' => $student_id
        ]);
    }

    public function basicDetails($student_id)
    {
        $details = StudentProfile::where('student_id', $student_id)->first();
        $primary_details = Student::where('id', $student_id)->first();

        if (!$details || !$primary_details) {
            return response()->json([
                'success' => false,
                'errors'  => 'Details not Found'
            ], 404);
        }

        // Convert models to arrays
        $detailsArray = $details->toArray();
        $primaryArray = $primary_details->toArray();

        // Fix DOB formatting
        $detailsArray['dob'] = $details->dob
            ? Carbon::parse($details->dob)->format('d-M-Y')
            : '-';

        // Fix Admission Date formatting
        $primaryArray['admission_date'] = $primary_details->admission_date
            ? Carbon::parse($primary_details->admission_date)->format('d-M-Y')
            : '-';

        return response()->json([
            'success' => true,
            'data' => $detailsArray,
            'primary_details' => $primaryArray,
        ]);
    }

    public function updateDetails(Request $request, $student_id)
    {
        $rules = [
            'first_name'          => 'sometimes|string|max:70',
            'middle_name'         => 'nullable|string|max:70',
            'last_name'           => 'sometimes|string|max:70',
            'dob'                 => 'sometimes|date',
            'gender'              => 'sometimes|in:male,female,other,unspecified',
            'blood_group'         => 'nullable|string|max:10',
            'religion'            => 'nullable|string|max:100',
            'caste'               => 'nullable|string|max:100',
            'nationality'         => 'nullable|string|max:70',
            'mother_tongue'       => 'nullable|string|max:70',
            'guardian_name'       => 'nullable|string|max:150',
            'guardian_relation'   => 'nullable|string|max:100',
            'guardian_phone'      => 'nullable|string|max:20',
            'guardian_email'      => 'nullable|email|max:150',
            'guardian_occupation' => 'nullable|string|max:100',
            'parent_income'       => 'nullable|numeric',
            'current_class'       => 'nullable|string|max:40',
            'section'             => 'nullable|string|max:10',
            'roll_no'             => 'nullable|string|max:30',
            'enrollment_status'   => 'nullable|string|max:50',
            'scholarship_status'  => 'nullable|string|max:50',
            'extracurriculars'    => 'nullable|array',
            'avatar_url'          => 'nullable|file|image|max:5120',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Student with profile (so we can use student_uid)
        $student = Student::with('profile')->find($student_id);

        if (!$student || !$student->profile) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found'
            ], 404);
        }

        $profile = $student->profile;
        $data = $request->only(array_keys($rules));

        // Handle avatar update
        if ($request->hasFile('avatar_url')) {
            $file = $request->file('avatar_url');
            $extension = $file->getClientOriginalExtension();

            // Filename = student_uid + datetime + extension
            $fileName = ($student->student_uid ?? 'student') . '_' . now()->format('Ymd_His') . '.' . $extension;

            // Delete old file if exists
            if (!empty($profile->avatar_url)) {
                $oldPath = storage_path('app/public/' . $profile->avatar_url);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Store new file
            $file->storeAs('StudentImages', $fileName, 'public');

            // Save relative path (without /storage prefix in DB)
            $data['avatar_url'] = "StudentImages/{$fileName}";
        }

        if (!empty($data)) {
            $profile->update($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Student profile updated successfully',
            'data'    => $profile
        ]);
    }




    public function listAll()
    {
        $students = Student::with('profile')->select('students.*');

        return DataTables::of($students)
            ->addColumn('avatar', function ($student) {
                if ($student->profile && $student->profile->avatar_url) {
                    return '<img src="' . asset('storage/' . $student->profile->avatar_url) . '" width="50" height="50" />';
                }
                return '-';
            })
            ->addColumn('full_name', function ($student) {
                $profile = $student->profile;
                if ($profile) {
                    return $profile->first_name . ' ' . ($profile->middle_name ?? '') . ' ' . $profile->last_name;
                }
                return '-';
            })
            ->addColumn('roll_no', function ($student) {
                return $student->profile->roll_no ?? '-';
            })
            ->addColumn('class_section', function ($student) {
                $profile = $student->profile;
                return $profile ? ($profile->current_class . ' / ' . $profile->section) : '-';
            })
            ->addColumn('student_uid', function ($student) {
                return $student->student_uid ?? '-';
            })
            ->addColumn('primary_email', function ($student) {
                return $student->primary_email ?? '-';
            })
            ->addColumn('primary_phone', function ($student) {
                return $student->primary_phone ?? '-';
            })
            ->addColumn('status', function ($student) {
                return ucfirst($student->status ?? '-');
            })
            ->addColumn('admission_date', function ($student) {
                return $student->admission_date ? date('d-M-Y', strtotime($student->admission_date)) : '-';
            })
            ->addColumn('actions', function ($student) {
                $url = route("students.Studentlist.studentDetailsPage", $student->id);
                return '<a href="' . $url . '" class="btn btn-sm btn-success">Details</a>';
            })
            ->filter(function ($query) {
                if (request()->has('search') && !empty(request('search')['value'])) {
                    $search = request('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('student_uid', 'like', "%{$search}%")
                            ->orWhere('primary_phone', 'like', "%{$search}%")
                            ->orWhereHas('profile', function ($q2) use ($search) {
                                $q2->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->rawColumns(['avatar', 'actions']) // ✅ keep only one call
            ->make(true);
    }
}
