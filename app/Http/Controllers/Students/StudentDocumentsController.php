<?php

namespace App\Http\Controllers\Students;

use Carbon\Carbon;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentDocument;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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

    public function managedocument($id)
    {
        return view('Admin.Students.StudentProfile.ManageDocument', ['id' => $id]);
    }

    public function getDocuments($student_id)
    {
        $documents = StudentDocument::where('student_id', $student_id)->get()->map(function ($doc) {
            return [
                'id'              => $doc->id,
                'document_type'   => $doc->document_type,
                'document_number' => $doc->document_number,
                'issue_date'      => $doc->issue_date ? Carbon::parse($doc->issue_date)->format('d-M-Y') : null,
                'expiry_date'     => $doc->expiry_date ? Carbon::parse($doc->expiry_date)->format('d-M-Y') : null,
                'file_name'       => $doc->file_name,
                'file_url'        => $doc->file_url,
                'remarks'         => $doc->remarks,
            ];
        });

        return response()->json(['data' => $documents]);
    }

    public function getDocument($student_id, $doc_id)
    {
        $doc = StudentDocument::where('student_id', $student_id)->findOrFail($doc_id);


        return response()->json(['data' => $doc]);
    }
    public function deleteDocument($student_id, $doc_id)
    {
        $doc = StudentDocument::where('student_id', $student_id)->findOrFail($doc_id);
        $doc->delete();
        return response()->json(['success' => true]);
    }

    public function updateDocument(Request $request, $student_id, $doc_id)
    {
        $student   = Student::findOrFail($student_id);
        $document  = StudentDocument::where('student_id', $student_id)->findOrFail($doc_id);

        $validated = $request->validate([
            'document_type'   => 'required|string|max:191',
            'document_number' => 'required|string|max:191',
            'issue_date'      => 'nullable|date',
            'expiry_date'     => 'nullable|date',
            'file_name'       => 'nullable|string|max:191',
            'file_url'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remarks'         => 'nullable|string|max:1000',
        ]);

        // ✅ Handle file upload only if new file exists
        if ($request->hasFile('file_url')) {
            // Delete old file if exists
            if ($document->file_url && Storage::disk('public')->exists($document->file_url)) {
                Storage::disk('public')->delete($document->file_url);
            }

            // Save new file
            $file      = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();

            $fileName = ($student->student_uid ?? 'student') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('student_documents', $fileName, 'public');

            $validated['file_url'] = "student_documents/{$fileName}";

            // Keep old file_name if not provided
            if (empty($validated['file_name'])) {
                unset($validated['file_name']);
            }
        } else {
            // No new file → keep old file_url
            unset($validated['file_url']);
        }

        $document->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $document
        ], 200);
    }
}
