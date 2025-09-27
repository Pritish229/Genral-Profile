<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use App\Models\VendorMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VendorMediaController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddMedias', ['id' => $id]);
    }
    public function storeMedia(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

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
            $fileName = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            // Save in public disk
            $file->storeAs('vendor_media', $fileName, 'public');
            $validated['file_url'] = "vendor_media/{$fileName}";
        }

        // Ensure tags are stored as JSON
        $validated['tags'] = $validated['tags'] ?? [];

        // Create media
        $media = VendorMedia::create([
            'tenant_id'     => $vendor->tenant_id,
            'vendor_id'    => $vendor->id,
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

    private function normalizeTags(array $tags): array
    {
        return array_values(array_unique(array_map(function ($tag) {
            return trim(strtolower($tag)); // normalize to lowercase & trim
        }, $tags)));
    }

    private function formatMedia(VendorMedia $media)
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
}
