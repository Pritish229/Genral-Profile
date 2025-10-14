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
    /* -----------------------------------------------------------------
     *  View Routes (no JSON)
     * ----------------------------------------------------------------- */

    /**
     * Add-document page (shared for both profiles – the form itself decides the profile).
     */
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddDocument', ['id' => $id]);
    }

    /**
     * Business-specific “add-documents” page (if you still need a separate UI for business).
     */
    public function BusinessDocs($id, $business_id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessDocuments', [
            'id'          => $id,
            'business_id' => $business_id,
        ]);
    }

    /**
     * Individual document management page.
     */
    public function manageIndividual($id)
    {
        return view('Admin.Vendors.VendorProfile.ManageDocument', [
            'id'   => $id,
            'type' => 'individual', // kept for backward-compatible Blade/JS logic
        ]);
    }

    /**
     * Business document management page.
     */
    public function manageBusiness($id, $business_id)
    {
        return view('Admin.Vendors.VendorProfile.BusinessDocuments', [
            'id'          => $id,
            'business_id' => $business_id,
            'type'        => 'business', // kept for backward-compatible Blade/JS logic
        ]);
    }

    /* -----------------------------------------------------------------
     *  Individual Document CRUD (profile_type = 'individual')
     * ----------------------------------------------------------------- */

    /** Store – Individual */
    public function storeIndividualDocument(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

        $request->validate([
            'document_type'     => 'required|string|max:191',
            'document_number'   => 'required|string|max:191',
            'issue_date'        => 'nullable|date',
            'expiry_date'       => 'nullable|date',
            'file_name'         => 'nullable|string|max:191',
            'file_url'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remarks'           => 'nullable|string|max:1000',
            'issuing_authority' => 'nullable|string|max:191',
        ]);

        if (! $request->hasFile('file_url')) {
            return response()->json(['success' => false, 'message' => 'File is required.'], 422);
        }

        $file      = $request->file('file_url');
        $extension = $file->getClientOriginalExtension();
        $fileName  = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
        $file->storeAs('VendorDocuments', $fileName, 'public');
        $filePath  = "VendorDocuments/{$fileName}";

        $document = VendorDocument::create([
            'tenant_id'         => $vendor->tenant_id,
            'vendor_id'         => $vendor->id,
            'profile_type'      => 'individual',
            'document_type'     => $request->document_type,
            'document_number'   => $request->document_number,
            'issue_date'        => $request->issue_date,
            'expiry_date'       => $request->expiry_date,
            'file_name'         => $request->file_name ?? $file->getClientOriginalName(),
            'file_url'          => $filePath,
            'remarks'           => $request->remarks,
            'issuing_authority' => $request->issuing_authority,
            'business_id'       => null,
            'business_name'     => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document added successfully.',
            'data'    => $this->formatDocument($document),
        ], 201);
    }

    /** List – Individual */
    public function getIndividualDocuments($vendor_id)
    {
        Vendor::findOrFail($vendor_id);

        $documents = VendorDocument::where('vendor_id', $vendor_id)
            ->where('profile_type', 'individual')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $documents->map(fn ($d) => $this->formatDocument($d))->toArray(),
        ]);
    }

    /** Show – Individual */
    public function getIndividualDocument($vendor_id, $doc_id)
    {
        Vendor::findOrFail($vendor_id);

        $document = VendorDocument::where('vendor_id', $vendor_id)
            ->where('id', $doc_id)
            ->where('profile_type', 'individual')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => $this->formatDocument($document),
        ]);
    }

    /** Update – Individual */
    public function updateIndividualDocument(Request $request, $vendor_id, $doc_id)
    {
        $vendor   = Vendor::findOrFail($vendor_id);
        $document = VendorDocument::where('vendor_id', $vendor_id)
            ->where('id', $doc_id)
            ->where('profile_type', 'individual')
            ->firstOrFail();

        $request->validate([
            'document_type'     => 'required|string|max:191',
            'document_number'   => 'required|string|max:191',
            'issue_date'        => 'nullable|date',
            'expiry_date'       => 'nullable|date',
            'file_name'         => 'nullable|string|max:191',
            'file_url'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remarks'           => 'nullable|string|max:1000',
            'issuing_authority' => 'nullable|string|max:191',
        ]);

        $data = [
            'document_type'     => $request->document_type,
            'document_number'   => $request->document_number,
            'issue_date'        => $request->issue_date,
            'expiry_date'       => $request->expiry_date,
            'file_name'         => $request->file_name,
            'remarks'           => $request->remarks,
            'issuing_authority' => $request->issuing_authority,
        ];

        if ($request->hasFile('file_url')) {
            if ($document->file_url && Storage::disk('public')->exists($document->file_url)) {
                Storage::disk('public')->delete($document->file_url);
            }

            $file      = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();
            $fileName  = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('VendorDocuments', $fileName, 'public');
            $data['file_url'] = "VendorDocuments/{$fileName}";
        }

        $document->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully.',
            'data'    => $this->formatDocument($document),
        ]);
    }

    /** Delete – Individual */
    public function deleteIndividualDocument($vendor_id, $doc_id)
    {
        Vendor::findOrFail($vendor_id);

        $document = VendorDocument::where('vendor_id', $vendor_id)
            ->where('id', $doc_id)
            ->where('profile_type', 'individual')
            ->firstOrFail();

        if ($document->file_url && Storage::disk('public')->exists($document->file_url)) {
            Storage::disk('public')->delete($document->file_url);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.',
        ]);
    }

    /* -----------------------------------------------------------------
     *  Business Document CRUD (profile_type = 'business')
     * ----------------------------------------------------------------- */

    /** Store – Business */
    public function storeBusinessDocument(Request $request, $vendor_id, $business_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        $business = VendorBusinessProfile::where('vendor_id', $vendor_id)
            ->where('id', $business_id)
            ->firstOrFail();

        $request->validate([
            'document_type'     => 'required|string|max:191',
            'document_number'   => 'required|string|max:191',
            'issue_date'        => 'nullable|date',
            'expiry_date'       => 'nullable|date',
            'file_name'         => 'nullable|string|max:191',
            'file_url'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remarks'           => 'nullable|string|max:1000',
            'issuing_authority' => 'nullable|string|max:191',
        ]);

        if (! $request->hasFile('file_url')) {
            return response()->json(['success' => false, 'message' => 'File is required.'], 422);
        }

        $file      = $request->file('file_url');
        $extension = $file->getClientOriginalExtension();
        $fileName  = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
        $file->storeAs('VendorBusinessDocuments', $fileName, 'public');
        $filePath  = "VendorBusinessDocuments/{$fileName}";

        $document = VendorDocument::create([
            'tenant_id'         => $vendor->tenant_id,
            'vendor_id'         => $vendor->id,
            'profile_type'      => 'business',
            'document_type'     => $request->document_type,
            'document_number'   => $request->document_number,
            'issue_date'        => $request->issue_date,
            'expiry_date'       => $request->expiry_date,
            'file_name'         => $request->file_name ?? $file->getClientOriginalName(),
            'file_url'          => $filePath,
            'remarks'           => $request->remarks,
            'issuing_authority' => $request->issuing_authority,
            'business_id'       => $business->id,
            'business_name'     => $business->trade_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document added successfully.',
            'data'    => $this->formatDocument($document),
        ], 201);
    }

    /** List – Business (specific to business_id) */
    public function getBusinessDocuments($vendor_id, $business_id)
    {
        Vendor::findOrFail($vendor_id);
        VendorBusinessProfile::where('vendor_id', $vendor_id)->where('id', $business_id)->firstOrFail();

        $documents = VendorDocument::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('profile_type', 'business')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $documents->map(fn ($d) => $this->formatDocument($d))->toArray(),
        ]);
    }

    /** Show – Business */
    public function getBusinessDocument($vendor_id, $business_id, $doc_id)
    {
        Vendor::findOrFail($vendor_id);
        VendorBusinessProfile::where('vendor_id', $vendor_id)->where('id', $business_id)->firstOrFail();

        $document = VendorDocument::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('id', $doc_id)
            ->where('profile_type', 'business')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => $this->formatDocument($document),
        ]);
    }

    /** Update – Business */
    public function updateBusinessDocument(Request $request, $vendor_id, $business_id, $doc_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);
        VendorBusinessProfile::where('vendor_id', $vendor_id)->where('id', $business_id)->firstOrFail();

        $document = VendorDocument::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('id', $doc_id)
            ->where('profile_type', 'business')
            ->firstOrFail();

        $request->validate([
            'document_type'     => 'required|string|max:191',
            'document_number'   => 'required|string|max:191',
            'issue_date'        => 'nullable|date',
            'expiry_date'       => 'nullable|date',
            'file_name'         => 'nullable|string|max:191',
            'file_url'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remarks'           => 'nullable|string|max:1000',
            'issuing_authority' => 'nullable|string|max:191',
        ]);

        $data = [
            'document_type'     => $request->document_type,
            'document_number'   => $request->document_number,
            'issue_date'        => $request->issue_date,
            'expiry_date'       => $request->expiry_date,
            'file_name'         => $request->file_name,
            'remarks'           => $request->remarks,
            'issuing_authority' => $request->issuing_authority,
        ];

        if ($request->hasFile('file_url')) {
            if ($document->file_url && Storage::disk('public')->exists($document->file_url)) {
                Storage::disk('public')->delete($document->file_url);
            }

            $file      = $request->file('file_url');
            $extension = $file->getClientOriginalExtension();
            $fileName  = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('VendorBusinessDocuments', $fileName, 'public');
            $data['file_url'] = "VendorBusinessDocuments/{$fileName}";
        }

        $document->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully.',
            'data'    => $this->formatDocument($document),
        ]);
    }

    /** Delete – Business */
    public function deleteBusinessDocument($vendor_id, $business_id, $doc_id)
    {
        Vendor::findOrFail($vendor_id);
        VendorBusinessProfile::where('vendor_id', $vendor_id)->where('id', $business_id)->firstOrFail();

        $document = VendorDocument::where('vendor_id', $vendor_id)
            ->where('business_id', $business_id)
            ->where('id', $doc_id)
            ->where('profile_type', 'business')
            ->firstOrFail();

        if ($document->file_url && Storage::disk('public')->exists($document->file_url)) {
            Storage::disk('public')->delete($document->file_url);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.',
        ]);
    }


    public function businessDocuments($vendor_id, $business_id)
    {
        return $this->getBusinessDocuments($vendor_id, $business_id);
    }


    private function formatDocument($document)
    {
        // Also expose the raw Y-m-d values – useful for flatpickr editing
        return [
            'id'                => $document->id,
            'vendor_id'         => $document->vendor_id,
            'business_id'       => $document->business_id,
            'profile_type'      => $document->profile_type,
            'document_type'     => $document->document_type,
            'document_number'   => $document->document_number,
            'issue_date'        => $document->issue_date ? Carbon::parse($document->issue_date)->format('d F Y') : null,
            'issue_date_raw'    => $document->issue_date, // Y-m-d for flatpickr
            'expiry_date'       => $document->expiry_date ? Carbon::parse($document->expiry_date)->format('d F Y') : null,
            'expiry_date_raw'   => $document->expiry_date,
            'file_name'         => $document->file_name,
            'file_url'          => asset('storage/' . $document->file_url),
            'remarks'           => $document->remarks,
            'issuing_authority' => $document->issuing_authority,
            'business_name'     => $document->business_name,
            'created_at'        => $document->created_at->format('d M Y, h:i A'),
            'updated_at'        => $document->updated_at->format('d M Y, h:i A'),
        ];
    }
}