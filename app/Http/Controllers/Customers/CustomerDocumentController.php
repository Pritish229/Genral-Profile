<?php

namespace App\Http\Controllers\Customers;

use Carbon\Carbon;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerDocument;
use App\Models\Customer\CustomerBusinessProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CustomerDocumentController extends Controller
{
    public function index($id)
    {
        return view('Admin.Customers.CustomerProfile.AddDocument', ['id' => $id]);
    }

    /**
     * Business-specific “add-documents” page (if you still need a separate UI for business).
     */
    public function BusinessDocs($id, $business_id)
    {
        return view('Admin.Customers.CustomerProfile.BusinessDocuments', [
            'id'          => $id,
            'business_id' => $business_id,
        ]);
    }

    /**
     * Individual document management page.
     */
    public function manageIndividual($id)
    {
        return view('Admin.Customers.CustomerProfile.ManageDocument', [
            'id'   => $id,
            'type' => 'individual', // kept for backward-compatible Blade/JS logic
        ]);
    }

    /**
     * Business document management page.
     */
    public function manageBusiness($id, $business_id)
    {
        return view('Admin.Customers.CustomerProfile.BusinessDocuments', [
            'id'          => $id,
            'business_id' => $business_id,
            'type'        => 'business', // kept for backward-compatible Blade/JS logic
        ]);
    }

    /** Store – Individual */
    public function storeIndividualDocument(Request $request, $customer_id)
    {
        $customer = Customer::findOrFail($customer_id);

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
        $fileName  = ($customer->customer_uid ?? 'customer') . '_' . now()->format('Ymd_His') . '.' . $extension;
        $file->storeAs('CustomerDocuments', $fileName, 'public');
        $filePath  = "CustomerDocuments/{$fileName}";

        $document = CustomerDocument::create([
            'tenant_id'         => $customer->tenant_id,
            'customer_id'         => $customer->id,
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
    public function getIndividualDocuments($customer_id)
    {
        Customer::findOrFail($customer_id);

        $documents = CustomerDocument::where('customer_id', $customer_id)
            ->where('profile_type', 'individual')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $documents->map(fn ($d) => $this->formatDocument($d))->toArray(),
        ]);
    }

    /** Show – Individual */
    public function getIndividualDocument($customer_id, $doc_id)
    {
        Customer::findOrFail($customer_id);

        $document = CustomerDocument::where('customer_id', $customer_id)
            ->where('id', $doc_id)
            ->where('profile_type', 'individual')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => $this->formatDocument($document),
        ]);
    }

    /** Update – Individual */
    public function updateIndividualDocument(Request $request, $customer_id, $doc_id)
    {
        $customer   = Customer::findOrFail($customer_id);
        $document = CustomerDocument::where('customer_id', $customer_id)
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
            $fileName  = ($customer->customer_uid ?? 'customer') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('CustomerDocuments', $fileName, 'public');
            $data['file_url'] = "CustomerDocuments/{$fileName}";
        }

        $document->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully.',
            'data'    => $this->formatDocument($document),
        ]);
    }

    /** Delete – Individual */
    public function deleteIndividualDocument($customer_id, $doc_id)
    {
        Customer::findOrFail($customer_id);

        $document = CustomerDocument::where('customer_id', $customer_id)
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


    /** Store – Business */
    public function storeBusinessDocument(Request $request, $customer_id, $business_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $business = CustomerBusinessProfile::where('customer_id', $customer_id)
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
        $fileName  = ($customer->customer_uid ?? 'customer') . '_' . now()->format('Ymd_His') . '.' . $extension;
        $file->storeAs('CustomerBusinessDocuments', $fileName, 'public');
        $filePath  = "CustomerBusinessDocuments/{$fileName}";

        $document = CustomerDocument::create([
            'tenant_id'         => $customer->tenant_id,
            'customer_id'         => $customer->id,
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
    public function getBusinessDocuments($customer_id, $business_id)
    {
        Customer::findOrFail($customer_id);
        CustomerBusinessProfile::where('customer_id', $customer_id)->where('id', $business_id)->firstOrFail();

        $documents = CustomerDocument::where('customer_id', $customer_id)
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
    public function getBusinessDocument($customer_id, $business_id, $doc_id)
    {
        Customer::findOrFail($customer_id);
        CustomerBusinessProfile::where('customer_id', $customer_id)->where('id', $business_id)->firstOrFail();

        $document = CustomerDocument::where('customer_id', $customer_id)
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
    public function updateBusinessDocument(Request $request, $customer_id, $business_id, $doc_id)
    {
        $customer = Customer::findOrFail($customer_id);
        CustomerBusinessProfile::where('customer_id', $customer_id)->where('id', $business_id)->firstOrFail();

        $document = CustomerDocument::where('customer_id', $customer_id)
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
            $fileName  = ($customer->customer_uid ?? 'customer') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('CustomerBusinessDocuments', $fileName, 'public');
            $data['file_url'] = "CustomerBusinessDocuments/{$fileName}";
        }

        $document->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully.',
            'data'    => $this->formatDocument($document),
        ]);
    }

    /** Delete – Business */
    public function deleteBusinessDocument($customer_id, $business_id, $doc_id)
    {
        Customer::findOrFail($customer_id);
        CustomerBusinessProfile::where('customer_id', $customer_id)->where('id', $business_id)->firstOrFail();

        $document = CustomerDocument::where('customer_id', $customer_id)
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


    public function businessDocuments($customer_id, $business_id)
    {
        return $this->getBusinessDocuments($customer_id, $business_id);
    }


    private function formatDocument($document)
    {
        // Also expose the raw Y-m-d values – useful for flatpickr editing
        return [
            'id'                => $document->id,
            'customer_id'         => $document->customer_id,
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
