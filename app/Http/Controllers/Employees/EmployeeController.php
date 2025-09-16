<?php

namespace App\Http\Controllers\Employees;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\EmployeeAddress;
use App\Models\EmployeeContact;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function create()
    {
        return view('Admin.Employees.EmployeeProfile.AddEmployees');
    }

    public function store(Request $request)
    {
        $rules = [
            'employee_uid'        => 'nullable|string|max:50',
            'primary_email'      => 'required|email|max:150',
            'primary_phone'      => 'required|max:30',
            'hire_date'     => 'required|date',
            'notes'              => 'nullable|string',
            'avatar_url'         => 'nullable|file|image|max:5120',
            'first_name'         => 'required|string|max:70',
            'middle_name'        => 'nullable|string|max:70',
            'last_name'          => 'required|string|max:70',
            'dob'                => 'required|date',
            'gender'             => 'required|in:male,female,other,unspecified',

        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            $employee = Employee::create([
                'tenant_id'         => 1, // adjust if multi-tenant
                'primary_email'     => $validated['primary_email'],
                'primary_phone'     => $validated['primary_phone'],
                'hire_date'    => $validated['hire_date'],
                'notes'             => $validated['notes'] ?? null,
            ]);

            $profile = EmployeeProfile::create([
                'employee_id'  => $employee->id,
                'tenant_id'   => $employee->tenant_id,
                'employee_uid' => $validated['employee_uid'] ?? null,
                'first_name'  => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name'   => $validated['last_name'],
                'dob'         => $validated['dob'],
                'gender'      => $validated['gender'],
                'notes'       => $validated['notes'] ?? null,
                'full_name'   => trim(preg_replace(
                    '/\s+/',
                    ' ',
                    ($validated['first_name'] ?? '') . ' ' .
                        (($validated['middle_name'] ?? '') ? ($validated['middle_name'] . ' ') : '') .
                        ($validated['last_name'] ?? '')
                )),
            ]);

            EmployeeContact::insert([
                [
                    'employee_id'    => $employee->id,
                    'tenant_id'     => $employee->tenant_id,
                    'contact_type'  => 'email',
                    'value'         => $employee->primary_email,
                    'is_primary'    => 0,
                    'is_emergency'  => 0,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ],
                [
                    'employee_id'    => $employee->id,
                    'tenant_id'     => $employee->tenant_id,
                    'contact_type'  => 'phone',
                    'value'         => $employee->primary_phone,
                    'is_primary'    => 1,
                    'is_emergency'  => 1,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]
            ]);

            EmployeeAddress::create([
                'employee_id' => $employee->id,
                'tenant_id'  => $employee->tenant_id,
            ]);



            // 5) Handle avatar upload if present
            if ($request->hasFile('avatar_url')) {
                $file      = $request->file('avatar_url');
                $extension = $file->getClientOriginalExtension();

                // filename = employee_uid + datetime + extension
                $fileName = ($validated['employee_uid'] ?? 'employee') . '_' . now()->format('Ymd_His') . '.' . $extension;

                // store file inside storage/app/public/employeeImages/
                $file->storeAs('employeeImages', $fileName, 'public');

                // save relative path (without /storage prefix)
                $profile->update([
                    'avatar_url' => "employeeImages/{$fileName}",
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully',
                'data'    => $employee
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while creating employee',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
