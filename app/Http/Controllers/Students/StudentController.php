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
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function create()
    {
        return view('Admin.students.StudentProfile.AddStudent');
    }
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'student_uid'        => 'nullable|string|max:50',
        'primary_email'      => 'required|email|max:150',
        'primary_phone'      => 'required|max:30',
        'admission_no'       => 'required|string|max:50',
        'univ_admission_no'  => 'required|string|max:50',
        'admission_date'     => 'required|date',
        'notes'              => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors'  => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();

    try {
        $student = Student::create([
            'tenant_id' => '1', 
        ]);


        StudentProfile::create(array_merge(
            $validator->validated(),
            [
                'student_id' => $student->id,
                'tenant_id'  => $student->tenant_id,
            ]
        ));
        StudentContact::create(array_merge(
            $validator->validated(),
            [
                'student_id' => $student->id,
                'tenant_id'  => $student->tenant_id,
            ]
        ));
        StudentAddress::create(array_merge(
            $validator->validated(),
            [
                'student_id' => $student->id,
                'tenant_id'  => $student->tenant_id,
            ]
        ));
        StudentDocument::create(array_merge(
            $validator->validated(),
            [
                'student_id' => $student->id,
                'tenant_id'  => $student->tenant_id,
            ]
        ));
        
        StudentPaymentAccount::create(array_merge(
            $validator->validated(),
            [
                'student_id' => $student->id,
                'tenant_id'  => $student->tenant_id,
            ]
        ));
         StudentMedia::create(array_merge(
            $validator->validated(),
            [
                'student_id' => $student->id,
                'tenant_id'  => $student->tenant_id,
            ]
        ));
        
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully',
            'data'    => $student
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong while creating student',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

}
