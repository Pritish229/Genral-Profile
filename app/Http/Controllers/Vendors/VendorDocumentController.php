<?php

namespace App\Http\Controllers\Vendors;

use Carbon\Carbon;
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
    public function BusinessDocs($id, $business_id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessDocuments', ['id' => $id, 'business_id' => $business_id]);
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

        // ✅ Choose folder based on vendor type
        $folder = $vendor->type === 'business' ? 'VendorBusinessDocuments' : 'VendorDocuments';

        // ✅ Handle file upload
        if ($request->hasFile('file_url')) {
            $file      = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();
            $fileName  = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;

            $file->storeAs($folder, $fileName, 'public');
            $validated['file_url'] = "{$folder}/{$fileName}";
        }

        // ✅ Always set profile_type from vendor
        $validated['profile_type'] = $vendor->type;

        // ✅ Vendor reference
        $validated['tenant_id'] = $vendor->tenant_id;
        $validated['vendor_id'] = $vendor->id;

        // ✅ If vendor is a business, attach business profile
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

        // ✅ Create document record
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

    public function getDocument($vendor_id, $doc_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $document = VendorDocument::where('vendor_id', $vendor_id)
            ->where('id', $doc_id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => $document
        ], 200);
    }



    public function updateDocument(Request $request, $vendor_id, $doc_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $document = VendorDocument::where('vendor_id', $vendor_id)
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

        // Set profile_type automatically
        $validated['profile_type'] = $vendor->type;

        // Decide folder based on vendor type
        $folder = $vendor->type === 'individual' ? 'VendorMedia' : 'VendorBusiness';

        // Handle file upload if a new file is provided
        if ($request->hasFile('file_url')) {
            // Delete old file if it exists
            if ($document->file_url && Storage::disk('public')->exists($document->file_url)) {
                Storage::disk('public')->delete($document->file_url);
            }

            $file = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();

            // Generate clean filename
            $fileName = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;

            // Store file
            $file->storeAs($folder, $fileName, 'public');
            $validated['file_url'] = "{$folder}/{$fileName}";
        }

        // Update document
        $document->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully.',
            'data'    => $document->fresh(),
        ], 200);
    }


    public function deleteDocument($vendor_id, $doc_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $document = VendorDocument::where('vendor_id', $vendor_id)
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

    public function businessDocuments($vendor_id, $business_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)
            ->where('id', $business_id)
            ->firstOrFail();

        if ($vendor->type !== 'business' && !$business) {
            return response()->json([
                'success' => false,
                'message' => 'This vendor does not have a business profile.'
            ], 422);
        }

        $documents = VendorDocument::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'vendor_id' => $doc->vendor_id,
                    'business_id' => $doc->business_id,
                    'profile_type' => $doc->profile_type,
                    'document_type' => $doc->document_type,
                    'document_number' => $doc->document_number,
                    'issue_date' => $doc->issue_date
                        ? Carbon::parse($doc->issue_date)->format('d F Y')
                        : null,
                    'expiry_date' => $doc->expiry_date
                        ? Carbon::parse($doc->expiry_date)->format('d F Y')
                        : null,
                    'file_path' => $doc->file_path,
                    'created_at' => $doc->created_at->format('d M Y, h:i A'),
                    'updated_at' => $doc->updated_at->format('d M Y, h:i A'),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $documents
        ], 200);
    }
}
