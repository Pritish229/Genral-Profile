<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use App\Models\VendorMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;
use Illuminate\Support\Facades\Storage;

class VendorMediaController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddMedias', ['id' => $id]);
    }

    public function manage($id, $type)
    {
        return view('Admin.Vendors.VendorProfile.ManageMedia', [
            'id' => $id,
            'type' => $type
        ]);
    }
    public function storeMedia(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $rules = [
            'media_usage'  => 'required|in:profile,logo,banner,gallery,kyc,doc_scan,other',
            'subject_name' => 'nullable|string|max:191',
            'file_name'    => 'nullable|string|max:191',
            'file_url'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'caption'      => 'nullable|string|max:500',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:50',
        ];

        $validated = $request->validate($rules);

        if ($request->hasFile('file_url')) {
            $file      = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();
            $fileName  = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('VendorMedia', $fileName, 'public');
            $validated['file_url'] = "VendorMedia/{$fileName}";
        }

        $validated['tags'] = $validated['tags'] ?? [];

        // Use the profile_type from the form if provided, otherwise use vendor type
        $profile_type = $request->input('profile_type', $vendor->type);

        if ($vendor->type === 'business') {
            $business = VendorBusinessProfile::where('vendor_id', $vendor->id)->first();

            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found for this vendor.'
                ], 422);
            }

            $validated['business_id']   = $business->id;
            $validated['business_name'] = $business->trade_name;
        }

        $media = VendorMedia::create([
            'tenant_id'    => $vendor->tenant_id,
            'vendor_id'    => $vendor->id,
            'profile_type' => $profile_type,
            'media_usage'  => $validated['media_usage'],
            'subject_name' => $validated['subject_name'] ?? null,
            'file_name'    => $validated['file_name'] ?? ($file->getClientOriginalName() ?? null),
            'file_url'     => $validated['file_url'],
            'caption'      => $validated['caption'] ?? null,
            'tags'         => json_encode($this->normalizeTags($validated['tags'])),
            'status'       => 'active',
            'business_id'  => $validated['business_id'] ?? null,
            'business_name' => $validated['business_name'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $this->formatMedia($media)
        ], 201);
    }

    public function getMedias($vendor_id, $type)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        // Filter medias by the specific profile type
        $medias = VendorMedia::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $medias->map(function ($media) {
                return $this->formatMedia($media);
            })
        ], 200);
    }

    public function getMedia($vendor_id, $type, $media_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $media = VendorMedia::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->where('id', $media_id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => $this->formatMedia($media)
        ], 200);
    }

    public function updateMedia(Request $request, $vendor_id, $type, $media_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $media = VendorMedia::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->where('id', $media_id)
            ->firstOrFail();

        $rules = [
            'media_usage'  => 'required|in:profile,logo,banner,gallery,kyc,doc_scan,other',
            'subject_name' => 'nullable|string|max:191',
            'file_name'    => 'nullable|string|max:191',
            'file_url'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'caption'      => 'nullable|string|max:500',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:50',
        ];

        $validated = $request->validate($rules);

        // Handle file upload if new file is provided
        if ($request->hasFile('file_url')) {
            // Delete old file if exists
            if ($media->file_url && Storage::disk('public')->exists($media->file_url)) {
                Storage::disk('public')->delete($media->file_url);
            }

            $file      = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();
            $fileName  = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('VendorMedia', $fileName, 'public');
            $validated['file_url'] = "VendorMedia/{$fileName}";
        }

        // Normalize tags
        if (isset($validated['tags'])) {
            $validated['tags'] = json_encode($this->normalizeTags($validated['tags']));
        }

        $media->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $this->formatMedia($media->fresh())
        ], 200);
    }

    public function deleteMedia($vendor_id, $type, $media_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $media = VendorMedia::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->where('id', $media_id)
            ->firstOrFail();

        // Delete associated file
        if ($media->file_url && Storage::disk('public')->exists($media->file_url)) {
            Storage::disk('public')->delete($media->file_url);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully'
        ], 200);
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
            'vendor_id'   => $media->vendor_id,
            'profile_type' => $media->profile_type,
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
