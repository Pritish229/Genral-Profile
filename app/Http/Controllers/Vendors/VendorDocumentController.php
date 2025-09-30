<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorDocument;
use App\Http\Controllers\Controller;
use App\Models\VendorBusinessProfile;

class VendorDocumentController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddDocument', ['id' => $id]);
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

        $validated['profile_type'] = $vendor->type;
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
}
