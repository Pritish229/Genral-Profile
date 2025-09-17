<?php

namespace App\Http\Controllers\Employees;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\EmployeeMedia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EmployeeMediaController extends Controller
{
    public function index($id)
    {
        return view('Admin.Employees.EmployeeProfile.AddMedias', ['id' => $id]);
    }

    public function manageindex($id)
    {
        return view('Admin.Employees.EmployeeProfile.ManageMedia', ['id' => $id]);
    }

    public function storeMedia(Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

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
            $fileName = ($employee->employee_uid ?? 'employee') . '_' . now()->format('Ymd_His') . '.' . $extension;

            // Save in public disk
            $file->storeAs('employee_media', $fileName, 'public');

            $validated['file_url'] = "employee_media/{$fileName}";
        }

        // Ensure tags are stored as JSON
        $validated['tags'] = $validated['tags'] ?? [];

        // Create media
        $media = EmployeeMedia::create([
            'tenant_id'     => $employee->tenant_id,
            'employee_id'    => $employee->id,
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

    public function getMedias($employee_id)
    {
        $medias = employeeMedia::where('employee_id', $employee_id)->latest()->get();

        $medias->transform(function ($media) {
            return $this->formatMedia($media);
        });

        return response()->json(['data' => $medias]);
    }

    public function show($employee_id, $media_id)
    {
        $media = employeeMedia::where('employee_id', $employee_id)->findOrFail($media_id);
        return response()->json(['data' => $this->formatMedia($media)]);
    }

    public function updateMedia(Request $request, $employee_id, $media_id)
    {
        $media = employeeMedia::where('employee_id', $employee_id)->findOrFail($media_id);

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
            $fileName = ($media->employee->employee_uid ?? 'employee') . '_' . now()->format('Ymd_His') . '.' . $ext;
            $file->storeAs('employee_media', $fileName, 'public');

            $validated['file_url'] = "employee_media/{$fileName}";
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

    public function destroy($employee_id, $media_id)
    {
        $media = employeeMedia::where('employee_id', $employee_id)->findOrFail($media_id);

        // Delete file
        if (!empty($media->file_url) && Storage::disk('public')->exists($media->file_url)) {
            Storage::disk('public')->delete($media->file_url);
        }

        $media->delete();

        return response()->json(['success' => true, 'message' => 'Media deleted successfully']);
    }

    // Helper to format media for response
    private function formatMedia(employeeMedia $media)
    {
        return [
            'id'          => $media->id,
            'tenant_id'   => $media->tenant_id,
            'employee_id'  => $media->employee_id,
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
