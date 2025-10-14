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
    // Plain view returns (no JSON wrapper)
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddMedias', ['id' => $id]);
    }

    public function manage($id)
    {
        return view('Admin.Vendors.VendorProfile.ManageMedia', ['id' => $id]);
    }

    public function businessMedia($id, $business_id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessMedia', ['id' => $id, 'business_id' => $business_id]);
    }

    // === Individual Media CRUD (profile_type = 'individual') ===

    // Insert (Store) - Individual
    public function storeIndividualMedia(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $request->validate([
            'media_usage'  => 'required|in:profile,logo,banner,gallery,kyc,doc_scan,other',
            'subject_name' => 'nullable|string|max:191',
            'file_name'    => 'nullable|string|max:191',
            'file_url'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'caption'      => 'nullable|string|max:500',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:50',
        ]);

        if (!$request->hasFile('file_url')) {
            return response()->json(['success' => false, 'message' => 'File is required.'], 422);
        }

        $file = $request->file('file_url');
        $extension = $file->getClientOriginalExtension();
        $fileName = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
        $file->storeAs('VendorMedia', $fileName, 'public');
        $filePath = "VendorMedia/{$fileName}";

        $tags = $request->tags ? json_encode(array_values(array_unique(array_map(fn($tag) => trim(strtolower($tag)), $request->tags)))) : json_encode([]);

        $media = VendorMedia::create([
            'tenant_id'     => $vendor->tenant_id,
            'vendor_id'     => $vendor->id,
            'profile_type'  => 'individual',
            'media_usage'   => $request->media_usage,
            'subject_name'  => $request->subject_name,
            'file_name'     => $request->file_name ?? $file->getClientOriginalName(),
            'file_url'      => $filePath,
            'caption'       => $request->caption,
            'tags'          => $tags,
            'status'        => 'active',
            'business_id'   => null,
            'business_name' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Media added successfully.',
            'data' => $this->formatMedia($media)
        ], 201);
    }

    // List - Individual
    public function getIndividualMedias($vendor_id)
    {
        Vendor::findOrFail($vendor_id);

        $medias = VendorMedia::where('vendor_id', $vendor_id)
            ->where('profile_type', 'individual')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $medias->map(fn($media) => $this->formatMedia($media))->toArray()
        ]);
    }

    // Details (Show) - Individual
    public function getIndividualMedia($vendor_id, $media_id)
    {
        Vendor::findOrFail($vendor_id);

        $media = VendorMedia::where('vendor_id', $vendor_id)
            ->where('id', $media_id)
            ->where('profile_type', 'individual')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatMedia($media)
        ]);
    }

    // Update - Individual
    public function updateIndividualMedia(Request $request, $vendor_id, $media_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $media = VendorMedia::where('vendor_id', $vendor_id)
            ->where('id', $media_id)
            ->where('profile_type', 'individual')
            ->firstOrFail();

        $request->validate([
            'media_usage'  => 'required|in:profile,logo,banner,gallery,kyc,doc_scan,other',
            'subject_name' => 'nullable|string|max:191',
            'file_name'    => 'nullable|string|max:191',
            'file_url'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'caption'      => 'nullable|string|max:500',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:50',
        ]);

        $data = [
            'media_usage'  => $request->media_usage,
            'subject_name' => $request->subject_name,
            'file_name'    => $request->file_name,
            'caption'      => $request->caption,
        ];

        if ($request->hasFile('file_url')) {
            if ($media->file_url && Storage::disk('public')->exists($media->file_url)) {
                Storage::disk('public')->delete($media->file_url);
            }
            $file = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();
            $fileName = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('VendorMedia', $fileName, 'public');
            $data['file_url'] = "VendorMedia/{$fileName}";
        }

        if ($request->has('tags')) {
            $data['tags'] = json_encode(array_values(array_unique(array_map(fn($tag) => trim(strtolower($tag)), $request->tags ?? []))));
        }

        $media->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Media updated successfully.',
            'data' => $this->formatMedia($media)
        ]);
    }

    // Delete - Individual
    public function deleteIndividualMedia($vendor_id, $media_id)
    {
        Vendor::findOrFail($vendor_id);

        $media = VendorMedia::where('vendor_id', $vendor_id)
            ->where('id', $media_id)
            ->where('profile_type', 'individual')
            ->firstOrFail();

        if ($media->file_url && Storage::disk('public')->exists($media->file_url)) {
            Storage::disk('public')->delete($media->file_url);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully.'
        ]);
    }

    // === Business Media CRUD (profile_type = 'business') ===

    // Insert (Store) - Business
    public function storeBusinessMedia(Request $request, $vendor_id, $business_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)
            ->where('id', $business_id)
            ->firstOrFail();

        $request->validate([
            'media_usage'  => 'required|in:profile,logo,banner,gallery,kyc,doc_scan,other',
            'subject_name' => 'nullable|string|max:191',
            'file_name'    => 'nullable|string|max:191',
            'file_url'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'caption'      => 'nullable|string|max:500',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:50',
        ]);

        if (!$request->hasFile('file_url')) {
            return response()->json(['success' => false, 'message' => 'File is required.'], 422);
        }

        $file = $request->file('file_url');
        $extension = $file->getClientOriginalExtension();
        $fileName = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
        $file->storeAs('VendorBusinessMedia', $fileName, 'public');
        $filePath = "VendorBusinessMedia/{$fileName}";

        $tags = $request->tags ? json_encode(array_values(array_unique(array_map(fn($tag) => trim(strtolower($tag)), $request->tags)))) : json_encode([]);

        $media = VendorMedia::create([
            'tenant_id'     => $vendor->tenant_id,
            'vendor_id'     => $vendor->id,
            'profile_type'  => 'business',
            'media_usage'   => $request->media_usage,
            'subject_name'  => $request->subject_name,
            'file_name'     => $request->file_name ?? $file->getClientOriginalName(),
            'file_url'      => $filePath,
            'caption'       => $request->caption,
            'tags'          => $tags,
            'status'        => 'active',
            'business_id'   => $business->id,
            'business_name' => $business->trade_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Media added successfully.',
            'data' => $this->formatMedia($media)
        ], 201);
    }

    // List - Business
    public function getBusinessMedias($vendor_id, $business_id)
    {
        Vendor::findOrFail($vendor_id);
        VendorBusinessProfile::where('vendor_id', $vendor_id)
            ->where('id', $business_id)
            ->firstOrFail();

        $medias = VendorMedia::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $medias->map(fn($media) => $this->formatMedia($media))->toArray()
        ]);
    }

    // Details (Show) - Business
    public function getBusinessMedia($vendor_id, $business_id, $media_id)
    {
        Vendor::findOrFail($vendor_id);
        VendorBusinessProfile::where('vendor_id', $vendor_id)
            ->where('id', $business_id)
            ->firstOrFail();

        $media = VendorMedia::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->where('id', $media_id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatMedia($media)
        ]);
    }

    // Update - Business
    public function updateBusinessMedia(Request $request, $vendor_id, $business_id, $media_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        VendorBusinessProfile::where('vendor_id', $vendor_id)
            ->where('id', $business_id)
            ->firstOrFail();

        $media = VendorMedia::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->where('id', $media_id)
            ->firstOrFail();

        $request->validate([
            'media_usage'  => 'required|in:profile,logo,banner,gallery,kyc,doc_scan,other',
            'subject_name' => 'nullable|string|max:191',
            'file_name'    => 'nullable|string|max:191',
            'file_url'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'caption'      => 'nullable|string|max:500',
            'tags'         => 'nullable|array',
            'tags.*'       => 'string|max:50',
        ]);

        $data = [
            'media_usage'  => $request->media_usage,
            'subject_name' => $request->subject_name,
            'file_name'    => $request->file_name,
            'caption'      => $request->caption,
        ];

        if ($request->hasFile('file_url')) {
            if ($media->file_url && Storage::disk('public')->exists($media->file_url)) {
                Storage::disk('public')->delete($media->file_url);
            }
            $file = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();
            $fileName = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('VendorBusinessMedia', $fileName, 'public');
            $data['file_url'] = "VendorBusinessMedia/{$fileName}";
        }

        if ($request->has('tags')) {
            $data['tags'] = json_encode(array_values(array_unique(array_map(fn($tag) => trim(strtolower($tag)), $request->tags ?? []))));
        }

        $media->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Media updated successfully.',
            'data' => $this->formatMedia($media)
        ]);
    }

    // Delete - Business
    public function deleteBusinessMedia($vendor_id, $business_id, $media_id)
    {
        Vendor::findOrFail($vendor_id);
        VendorBusinessProfile::where('vendor_id', $vendor_id)
            ->where('id', $business_id)
            ->firstOrFail();

        $media = VendorMedia::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->where('id', $media_id)
            ->firstOrFail();

        if ($media->file_url && Storage::disk('public')->exists($media->file_url)) {
            Storage::disk('public')->delete($media->file_url);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully.'
        ]);
    }

    // Helper to format media output
    private function formatMedia($media)
    {
        return [
            'id'            => $media->id,
            'vendor_id'     => $media->vendor_id,
            'profile_type'  => $media->profile_type,
            'media_usage'   => $media->media_usage,
            'subject_name'  => $media->subject_name,
            'file_name'     => $media->file_name,
            'file_url'      => asset('storage/' . $media->file_url),
            'caption'       => $media->caption,
            'tags'          => json_decode($media->tags, true) ?? [],
            'status'        => $media->status,
            'business_id'   => $media->business_id,
            'business_name' => $media->business_name,
            'created_at'    => $media->created_at->toDateTimeString(),
            'updated_at'    => $media->updated_at->toDateTimeString(),
        ];
    }
}