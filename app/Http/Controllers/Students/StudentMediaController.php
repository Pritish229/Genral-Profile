<?php

namespace App\Http\Controllers\Students;

use App\Models\Student;
use App\Models\StudentMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class StudentMediaController extends Controller
{
    public function index($id)
    {
        return view('Admin.Students.StudentProfile.AddMedias', ['id' => $id]);
    }

    public function manageindex($id)
    {
        return view('Admin.Students.StudentProfile.ManageMedia', ['id' => $id]);
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

            // Save in public disk
            $file->storeAs('student_media', $fileName, 'public');

            $validated['file_url'] = "student_media/{$fileName}";
        }

        // Ensure tags are stored as JSON
        $validated['tags'] = $validated['tags'] ?? [];

        // Create media
        $media = StudentMedia::create([
            'tenant_id'     => $student->tenant_id,
            'student_id'    => $student->id,
            'media_usage'   => $validated['media_usage'],
            'subject_name'  => $validated['subject_name'] ?? null,
            'file_name'     => $validated['file_name'] ?? $file->getClientOriginalName(),
            'file_url'      => $validated['file_url'],
            'caption'       => $validated['caption'] ?? null,
            'tags'          => json_encode($this->normalizeTags($validated['tags'] ?? [])),
            'status'        => 'active',
        ]);

        return response()->json(['success' => true, 'data' => $this->formatMedia($media)], 201);
    }

    public function getMedias($student_id)
    {
        $medias = StudentMedia::where('student_id', $student_id)->latest()->get();

        $medias->transform(function ($media) {
            return $this->formatMedia($media);
        });

        return response()->json(['data' => $medias]);
    }

    public function show($student_id, $media_id)
    {
        $media = StudentMedia::where('student_id', $student_id)->findOrFail($media_id);
        return response()->json(['data' => $this->formatMedia($media)]);
    }

    public function updateMedia(Request $request, $student_id, $media_id)
    {
        $media = StudentMedia::where('student_id', $student_id)->findOrFail($media_id);

        $validated = $request->validate([
            'media_usage'  => 'required|in:profile,banner,gallery,kyc,doc_scan,other',
            'subject_name' => 'nullable|string|max:191',
            'file_name'    => 'nullable|string|max:191',
            'file_url'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'caption'      => 'nullable|string|max:500',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:50',
        ]);

        // Handle new file upload
        if ($request->hasFile('file_url')) {
            // Delete old file
            if (!empty($media->file_url) && Storage::disk('public')->exists($media->file_url)) {
                Storage::disk('public')->delete($media->file_url);
            }

            $file = $request->file('file_url');
            $ext = $file->getClientOriginalExtension();
            $fileName = ($media->student->student_uid ?? 'student') . '_' . now()->format('Ymd_His') . '.' . $ext;
            $file->storeAs('student_media', $fileName, 'public');

            $validated['file_url'] = "student_media/{$fileName}";
        }

        $validated['tags'] = $validated['tags'] ?? [];

        $media->update([
            'media_usage'  => $validated['media_usage'],
            'subject_name' => $validated['subject_name'] ?? $media->subject_name,
            'file_name'    => $validated['file_name'] ?? $media->file_name,
            'file_url'     => $validated['file_url'] ?? $media->file_url,
            'caption'      => $validated['caption'] ?? $media->caption,
            'tags'         => json_encode($validated['tags']),
        ]);

        return response()->json(['success' => true, 'data' => $this->formatMedia($media)]);
    }

    public function destroy($student_id, $media_id)
    {
        $media = StudentMedia::where('student_id', $student_id)->findOrFail($media_id);

        // Delete file
        if (!empty($media->file_url) && Storage::disk('public')->exists($media->file_url)) {
            Storage::disk('public')->delete($media->file_url);
        }

        $media->delete();

        return response()->json(['success' => true, 'message' => 'Media deleted successfully']);
    }

    // Helper to format media for response
    private function formatMedia(StudentMedia $media)
    {
        return [
            'id'          => $media->id,
            'tenant_id'   => $media->tenant_id,
            'student_id'  => $media->student_id,
            'media_usage' => $media->media_usage,
            'subject_name' => $media->subject_name,
            'file_name'   => $media->file_name,
            'file_url'    => asset('storage/' . $media->file_url),
            'caption'     => $media->caption,
            'tags'        => is_array($media->tags) ? $media->tags : json_decode($media->tags, true),
            'status'      => $media->status,
            'created_at'  => $media->created_at,
            'updated_at'  => $media->updated_at,
        ];
    }

    private function normalizeTags($tags)
    {
        if (is_array($tags)) {
            return array_values(array_filter($tags, fn($t) => !empty(trim($t)))); // clean empties
        }

        if (is_string($tags)) {
            $decoded = json_decode($tags, true);
            return is_array($decoded) ? $decoded : [$tags]; // fallback: wrap string in array
        }

        return [];
    }
}
