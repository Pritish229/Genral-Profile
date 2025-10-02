<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorDocument;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;
use Illuminate\Support\Facades\Storage;

class VendorDocumentController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddDocument', ['id' => $id]);
    }

    public function manage($id, $type)
    {
        return view('Admin.Vendors.VendorProfile.ManageDocument', [
            'id' => $id,
            'type' => $type
        ]);
    }

    public function storeDocument(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $rules = [
            'document_type'     => 'required|string|max:191',
            'document_number'   => 'required|string|max:191',
            'issue_date'        => 'nullable|date',
            'expiry_date'       => 'nullable|date',
            'file_name'         => 'nullable|string|max:191',
            'file_url'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remarks'           => 'nullable|string|max:1000',
            'issuing_authority' => 'nullable|string|max:191',
        ];

        $validated = $request->validate($rules);

        if ($request->hasFile('file_url')) {
            $file      = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();
            $fileName  = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('VendorDocuments', $fileName, 'public');
            $validated['file_url'] = "VendorDocuments/{$fileName}";
        }

        // Use the profile_type from the form if provided, otherwise use vendor type
        $validated['profile_type'] = $request->input('profile_type', $vendor->type);
        $validated['tenant_id']    = $vendor->tenant_id;
        $validated['vendor_id']    = $vendor->id;

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

        $document = VendorDocument::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $document
        ], 200);
    }

    public function getDocuments($vendor_id, $type)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        // Filter documents by the specific profile type
        $documents = VendorDocument::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $documents
        ], 200);
    }

    public function getDocument($vendor_id, $type, $doc_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $document = VendorDocument::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->where('id', $doc_id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => $document
        ], 200);
    }

    public function updateDocument(Request $request, $vendor_id, $type, $doc_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $document = VendorDocument::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->where('id', $doc_id)
            ->firstOrFail();

        $rules = [
            'document_type'     => 'required|string|max:191',
            'document_number'   => 'required|string|max:191',
            'issue_date'        => 'nullable|date',
            'expiry_date'       => 'nullable|date',
            'file_name'         => 'nullable|string|max:191',
            'file_url'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remarks'           => 'nullable|string|max:1000',
            'issuing_authority' => 'nullable|string|max:191',
        ];

        $validated = $request->validate($rules);

        // Handle file upload if new file is provided
        if ($request->hasFile('file_url')) {
            // Delete old file if exists
            if ($document->file_url && Storage::disk('public')->exists($document->file_url)) {
                Storage::disk('public')->delete($document->file_url);
            }

            $file      = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();
            $fileName  = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('VendorDocuments', $fileName, 'public');
            $validated['file_url'] = "VendorDocuments/{$fileName}";
        }

        $document->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $document->fresh()
        ], 200);
    }

    public function deleteDocument($vendor_id, $type, $doc_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $document = VendorDocument::where('vendor_id', $vendor_id)
            ->where('profile_type', $type)
            ->where('id', $doc_id)
            ->firstOrFail();

        // Delete associated file
        if ($document->file_url && Storage::disk('public')->exists($document->file_url)) {
            Storage::disk('public')->delete($document->file_url);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ], 200);
    }
}
