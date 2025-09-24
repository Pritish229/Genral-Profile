<?php

namespace App\Http\Controllers\Vendors;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorContact;
use App\Http\Controllers\Controller;

class VendorContactController extends Controller
{
    public function index($id)
    {
        return view('Admin.Vendors.VendorProfile.AddContact', ['id' => $id]);
    }

    public function storeContact(Request $request, $vendor_id)
    {
        // Validate request
        $validated = $request->validate([
            'contact_type' => 'required|string|max:120',
            'value'        => 'required|string|max:120',
            'label'        => 'nullable|string|max:120',
        ]);

        $vendor = Vendor::findOrFail($vendor_id);

        $contact = VendorContact::create([
            'contact_type' => $validated['contact_type'],
            'label'        => $validated['label'] ?? null,
            'value'        => $validated['value'],
            'vendor_id'   => $vendor->id,
            'tenant_id'    => $vendor->tenant_id,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $contact
        ], 201);
    }

    public function permanentContact($vendor_id)
    {
        $contact = VendorContact::where('vendor_id', $vendor_id)->where('is_primary', '1')->first();
        if ($contact) {
            return response()->json([
                'success' => true,
                'message' => 'Contact Fatch successfully.',
                'data'    => $contact
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'employee contact not found',
            ], 404);
        }
    }
}
