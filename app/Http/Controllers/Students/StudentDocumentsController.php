<?php

namespace App\Http\Controllers\Students;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentDocument;
use App\Http\Controllers\Controller;

class StudentDocumentsController extends Controller
{
    public function index($id)
    {
        return view('Admin.Students.StudentProfile.AddDocument', ['id' => $id]);
    }
    public function storeDocument(Request $request, $student_id)
    {
        $student = Student::findOrFail($student_id);

        $validated = $request->validate([
            'document_type'   => 'required|string|max:191',
            'document_number' => 'required|string|max:191',
            'issue_date'      => 'nullable|date',
            'expiry_date'     => 'nullable|date',
            'file_name'       => 'nullable|string|max:191',
            'file_url'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remarks'         => 'nullable|string|max:1000',
        ]);

        // Handle file upload like your avatar example
        if ($request->hasFile('file_url')) {
            $file      = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();

            // filename = student_uid + datetime + extension
            $fileName = ($student->student_uid ?? 'student') . '_' . now()->format('Ymd_His') . '.' . $extension;

            // store file inside storage/app/public/student_documents/
            $file->storeAs('student_documents', $fileName, 'public');

            // save relative path (without /storage prefix)
            $validated['file_url'] = "student_documents/{$fileName}";
        }

        // Always create a new document
        $validated['tenant_id']  = $student->tenant_id;
        $validated['student_id'] = $student->id;

        $document = StudentDocument::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $document
        ], 200);
    }
}
