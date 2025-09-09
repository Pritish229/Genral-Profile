<?php

namespace App\Http\Controllers\Students;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentContact;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StudentContactController extends Controller
{
    public function index($id)
    {
        return view('Admin.Students.StudentProfile.AddContact', ['id' => $id]);
    }


    public function storeContact(Request $request, $student_id)
    {
        // Validate request
        $validated = $request->validate([
            'contact_type' => 'required|string|max:120',
            'value'        => 'required|string|max:120',
            'label'        => 'nullable|string|max:120',
        ]);

        $student = Student::findOrFail($student_id);

        $contact = StudentContact::create([
            'contact_type' => $validated['contact_type'],
            'label'        => $validated['label'] ?? null,
            'value'        => $validated['value'],
            'student_id'   => $student->id,
            'tenant_id'    => $student->tenant_id,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $contact
        ], 201);
    }

    public function permanentContact($student_id)
    {
        $contact = StudentContact::where('student_id', $student_id)->where('is_primary', '1')->first();
        if ($contact) {
            return response()->json([
                'success' => true,
                'message' => 'Contact Fatch successfully.',
                'data'    => $contact
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Student contact not found',
            ], 404);
        }
    }
}
