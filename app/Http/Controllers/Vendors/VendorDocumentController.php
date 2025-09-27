<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorDocument;
use App\Http\Controllers\Controller;

class VendorDocumentController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddDocument', ['id' => $id]);
    }

    public function storeDocument(Request $request, $vendor_id)
    {
        $vendor = Vendor::findOrFail($vendor_id);

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

            // filename = vendor_uid + datetime + extension
            $fileName = ($vendor->vendor_uid ?? 'vendor') . '_' . now()->format('Ymd_His') . '.' . $extension;

            // store file inside storage/app/public/vendor_documents/
            $file->storeAs('vendor_documents', $fileName, 'public');

            // save relative path (without /storage prefix)
            $validated['file_url'] = "vendor_documents/{$fileName}";
        }

        // Always create a new document
        $validated['profile_type']  = $vendor->profile_type;
        $validated['tenant_id']  = $vendor->tenant_id;
        $validated['vendor_id'] = $vendor->id;

        



        $document = VendorDocument::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $document
        ], 200);
    }
}
