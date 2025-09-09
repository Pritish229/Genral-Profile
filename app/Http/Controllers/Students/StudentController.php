<?php

namespace App\Http\Controllers\Students;

use App\Models\Student;
use App\Models\StudentMedia;
use Illuminate\Http\Request;
use App\Models\StudentAddress;
use App\Models\StudentContact;
use App\Models\StudentProfile;
use App\Models\StudentDocument;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\StudentPaymentAccount;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function create()
    {
        return view('Admin.students.StudentProfile.AddStudent');
    }


    public function store(Request $request)
    {
        // 1) Validation
        $rules = [
            'student_uid'        => 'nullable|string|max:50',
            'primary_email'      => 'required|email|max:150',
            'primary_phone'      => 'required|max:30',
            'admission_no'       => 'required|string|max:50',
            'univ_admission_no'  => 'required|string|max:50',
            'admission_date'     => 'required|date',
            'notes'              => 'nullable|string',
            'avatar_url'         => 'nullable|file|image|max:5120',
            'first_name'         => 'required|string|max:70',
            'middle_name'        => 'nullable|string|max:70',
            'last_name'          => 'required|string|max:70',
            'dob'                => 'required|date',
            'gender'             => 'required|in:male,female,other,unspecified',
            'caste'               => 'nullable|string|max:100',
            'religion'           => 'nullable|string|max:100',
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
            $student = Student::create([
                'tenant_id'         => 1, // adjust if multi-tenant
                'primary_email'     => $validated['primary_email'],
                'primary_phone'     => $validated['primary_phone'],
                'admission_no'      => $validated['admission_no'],
                'univ_admission_no' => $validated['univ_admission_no'],
                'admission_date'    => $validated['admission_date'],
                'notes'             => $validated['notes'] ?? null,
            ]);

            $profile = StudentProfile::create([
                'student_id'  => $student->id,
                'tenant_id'   => $student->tenant_id,
                'student_uid' => $validated['student_uid'] ?? null,
                'first_name'  => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name'   => $validated['last_name'],
                'dob'         => $validated['dob'],
                'gender'      => $validated['gender'],
                'notes'       => $validated['notes'] ?? null,
                'caste'       => $validated['caste'],
                'religion'    => $validated['religion'] ?? null,
                'full_name'   => trim(preg_replace(
                    '/\s+/',
                    ' ',
                    ($validated['first_name'] ?? '') . ' ' .
                        (($validated['middle_name'] ?? '') ? ($validated['middle_name'] . ' ') : '') .
                        ($validated['last_name'] ?? '')
                )),
            ]);

            StudentContact::insert([
                [
                    'student_id'    => $student->id,
                    'tenant_id'     => $student->tenant_id,
                    'contact_type'  => 'email',
                    'value'         => $student->primary_email,
                    'is_primary'    => 0,
                    'is_emergency'  => 0,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ],
                [
                    'student_id'    => $student->id,
                    'tenant_id'     => $student->tenant_id,
                    'contact_type'  => 'phone',
                    'value'         => $student->primary_phone,
                    'is_primary'    => 1,
                    'is_emergency'  => 1,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]
            ]);

            StudentAddress::create([
                'student_id' => $student->id,
                'tenant_id'  => $student->tenant_id,
            ]);

            

            // 5) Handle avatar upload if present
            if ($request->hasFile('avatar_url')) {
                $file      = $request->file('avatar_url');
                $extension = $file->getClientOriginalExtension();

                // filename = student_uid + datetime + extension
                $fileName = ($validated['student_uid'] ?? 'student') . '_' . now()->format('Ymd_His') . '.' . $extension;

                // store file inside storage/app/public/StudentImages/
                $file->storeAs('StudentImages', $fileName, 'public');

                // save relative path (without /storage prefix)
                $profile->update([
                    'avatar_url' => "StudentImages/{$fileName}",
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student created successfully',
                'data'    => $student
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while creating student',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
