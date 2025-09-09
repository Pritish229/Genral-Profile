<?php

namespace App\Http\Controllers\Students;

use App\Models\Student;
use App\Models\StudentMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentMediaController extends Controller
{
    public function index($id)
    {
        return view('Admin.Students.StudentProfile.AddMedias', ['id' => $id]);
    }

    public function storeMedia(Request $request, $student_id)
    {
        $student = Student::findOrFail($student_id);

        $validated = $request->validate([
            'media_usage'  => 'required|in:profile,banner,gallery,kyc,doc_scan,other',
            'subject_name' => 'nullable|string|max:191',
            'file_name'    => 'nullable|string|max:191',
            'file_url'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'caption'      => 'nullable|string|max:500',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:50',
        ]);

        // Handle file upload
        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();

            $fileName = ($student->student_uid ?? 'student') . '_' . now()->format('Ymd_His') . '.' . $extension;

            $file->storeAs('student_media', $fileName, 'public');

            // save relative path (without /storage prefix)
            $validated['file_url'] = "student_media/{$fileName}";
        }

        // Create new media record
        $media = StudentMedia::create([
            'tenant_id'     => $student->tenant_id,
            'student_id'    => $student->id,
            'media_usage'   => $validated['media_usage'],
            'subject_name'  => $validated['subject_name'] ?? null,
            'file_name'     => $validated['file_name'] ?? $file->getClientOriginalName(),
            'file_url'      => $validated['file_url'],
            'caption'       => $validated['caption'] ?? null,
            'tags'          => $validated['tags'] ?? [],
            'status'        => 'active',
        ]);

        return response()->json([
            'success' => true,
            'data'    => $media
        ], 201);
    }
}
