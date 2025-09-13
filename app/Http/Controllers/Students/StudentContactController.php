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

    public function manageContact($id)
    {
        return view('Admin.Students.StudentProfile.ManageContact', ['id' => $id]);
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

    public function getContacts($id)
    {
        $studentcontact = StudentContact::where('student_id', $id)->get();
        return response()->json([
            'success' => true,
            'data' => $studentcontact
        ]);
    }



    // ✅ Update existing contact
    public function updateContact(Request $request, Student $student, StudentContact $contact)
    {
        if ($contact->student_id !== $student->id) {
            return response()->json([
                'success' => false,
                'message' => 'This contact does not belong to the student.'
            ], 403);
        }

        $validated = $request->validate([
            'contact_type' => 'required|string|max:120',
            'value'        => 'required|string|max:120',
            'label'        => 'nullable|string|max:120',
        ]);

        $contact->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $contact
        ]);
    }

    // ✅ Delete (Soft Delete)
    public function deleteContact(Student $student, StudentContact $contact)
    {
        if ($contact->is_primary) {
            return response()->json([
                'success' => false,
                'message' => 'Primary contact cannot be deleted.'
            ], 403);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully.'
        ]);
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
