<?php

namespace App\Http\Controllers\Employees;

use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\EmployeeProfile;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class EmployeePrimaryController extends Controller
{
    public function index($id)
    {
        return view('Admin.Employees.EmployeeProfile.Basicinfo', ['id' => $id]);
    }
    public function manageDetail($id)
    {
        return view('Admin.Employees.EmployeeProfile.ManageBasicinfo', ['id' => $id]);
    }
    public function employeelist(Request $request)
    {
        return view('Admin.Employees.EmployeeProfile.EmployeeList');
    }

    public function managerList(Request $request)
    {
        $query = $request->get('q', '');
        $excludeId = $request->get('exclude_id'); // ✅ we’ll pass current employee id from Blade

        $managers = EmployeeProfile::query()
            ->when($query, function ($q) use ($query) {
                $q->where('full_name', 'like', "%$query%");
            })
            ->when($excludeId, function ($q) use ($excludeId) {
                $q->where('employee_id', '!=', $excludeId); // ✅ exclude current employee
            })
            ->select('id', 'employee_id', 'full_name')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $managers
        ]);
    }

    public function updateDetails(Request $request, $employee_id)
    {
        $rules = [
            'first_name'   => 'sometimes|string|max:70',
            'middle_name'  => 'nullable|string|max:70',
            'last_name'    => 'sometimes|string|max:70',

            'designation'       => 'nullable|string|max:255',
            'department'        => 'nullable|string|max:255',
            'employment_type'   => 'nullable|string|max:50',
            'salary_currency'   => 'nullable|string|max:3',
            'base_salary'       => 'nullable|numeric',
            'experience_years'  => 'nullable|string|max:10',

            'emergency_contact_name'  => 'nullable|string|max:70',
            'emergency_relation'      => 'nullable|string|max:70',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'location'                => 'nullable|string|max:70',

            'dob'          => 'sometimes|date',
            'gender'       => 'sometimes|in:male,female,other,unspecified',
            'blood_group'  => 'nullable|string|max:10',

            'skills'       => 'nullable|array',
            'skills.*'     => 'string|max:100',

            'avatar_url'   => 'nullable|file|image|max:5120',
            'manager_id'   => 'nullable|exists:employee_profiles,id',
        ];

        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $employee = Employee::with('profile')->find($employee_id);

        if (!$employee || !$employee->profile) {
            return response()->json([
                'success' => false,
                'message' => 'Employee profile not found'
            ], 404);
        }

        $profile = $employee->profile;

        // Collect all updatable fields except avatar (handled separately)
        $data = $request->only([
            'first_name',
            'middle_name',
            'last_name',
            'dob',
            'gender',
            'blood_group',
            'designation',
            'department',
            'employment_type',
            'salary_currency',
            'base_salary',
            'experience_years',
            'emergency_contact_name',
            'emergency_relation',
            'emergency_contact_phone',
            'location',
            'skills',
            'manager_id'
        ]);
        if ($request->has('skills')) {
            $data['skills'] = $request->skills;
        }

        // Handle skills as JSON
        if ($request->has('skills')) {
            $data['skills'] = json_encode($request->skills);
        }

        // Generate full_name dynamically
        $data['full_name'] = trim(preg_replace(
            '/\s+/',
            ' ',
            ($data['first_name'] ?? '') . ' ' .
                (!empty($data['middle_name']) ? $data['middle_name'] . ' ' : '') .
                ($data['last_name'] ?? '')
        ));

        // ✅ Handle avatar update
        if ($request->hasFile('avatar_url')) {
            $file = $request->file('avatar_url');
            $extension = $file->getClientOriginalExtension();
            $fileName = ($employee->employee_uid ?? 'employee') . '_' . now()->format('Ymd_His') . '.' . $extension;

            // Delete old file if exists
            if (!empty($profile->avatar_url)) {
                $oldPath = storage_path('app/public/' . $profile->avatar_url);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Store new file
            $file->storeAs('employeeImages', $fileName, 'public');

            $data['avatar_url'] = "employeeImages/{$fileName}";
        }

        // Update profile
        $profile->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Employee profile updated successfully',
            'data'    => $profile
        ]);
    }


    public function viewDetails($employee_id)
    {
        return view('Admin.Employees.EmployeeProfile.EmployeeDetails', ['id' => $employee_id]);
    }


    public function listAll()
    {
        $employees = Employee::with('profile')->select('employees.*');

        return DataTables::of($employees)
            ->addColumn('avatar', function ($employee) {
                if ($employee->profile && $employee->profile->avatar_url) {
                    return '<img src="' . asset('storage/' . $employee->profile->avatar_url) . '" width="50" height="50" />';
                }
                return '-';
            })
            ->addColumn('full_name', function ($employee) {
                $profile = $employee->profile;
                if ($profile) {
                    return $profile->first_name . ' ' . ($profile->middle_name ?? '') . ' ' . $profile->last_name;
                }
                return '-';
            })
            ->addColumn('roll_no', function ($employee) {
                return $employee->profile->roll_no ?? '-';
            })
            ->addColumn('class_section', function ($employee) {
                $profile = $employee->profile;
                return $profile ? ($profile->current_class . ' / ' . $profile->section) : '-';
            })
            ->addColumn('employee_uid', function ($employee) {
                return $employee->employee_uid ?? '-';
            })
            ->addColumn('primary_email', function ($employee) {
                return $employee->primary_email ?? '-';
            })
            ->addColumn('primary_phone', function ($employee) {
                return $employee->primary_phone ?? '-';
            })
            ->addColumn('status', function ($employee) {
                return ucfirst($employee->status ?? '-');
            })
            ->addColumn('hire_date', function ($employee) {
                return $employee->hire_date ? date('d-M-Y', strtotime($employee->hire_date)) : '-';
            })
            ->addColumn('actions', function ($employee) {
                $url = route("employees.viewDetails", $employee->id);
                return '<a href="' . $url . '" class="btn btn-sm btn-success">Details</a>';
            })
            ->filter(function ($query) {
                if (request()->has('search') && !empty(request('search')['value'])) {
                    $search = request('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('employee_uid', 'like', "%{$search}%")
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



    public function basicDetails($employee_id)
    {
        $details = EmployeeProfile::where('employee_id', $employee_id)->first();
        $primary_details = Employee::find($employee_id);

        if (!$details || !$primary_details) {
            return response()->json([
                'success' => false,
                'errors'  => 'Details not Found'
            ], 404);
        }

        // Convert models to arrays
        $detailsArray = $details ? $details->toArray() : [];
        $primaryArray = $primary_details ? $primary_details->toArray() : [];

        // Fix DOB formatting safely
        $detailsArray['dob'] = !empty($details->dob)
            ? Carbon::parse($details->dob)->format('d-M-Y')
            : null;

        if (isset($primary_details->hire_date)) {
            $primaryArray['hire_date'] = $primary_details->hire_date
                ? Carbon::parse($primary_details->hire_date)->format('d-M-Y')
                : null;
        }

        // ✅ Add Manager Info
        $manager = null;
        if (!empty($details->manager_id)) {
            $managerProfile = EmployeeProfile::find($details->manager_id);
            if ($managerProfile) {
                $manager = [
                    'id' => $managerProfile->id,
                    'full_name' => $managerProfile->full_name,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $detailsArray,
            'primary_details' => $primaryArray,
            'manager' => $manager // ✅ return manager object
        ]);
    }
}
