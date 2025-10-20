<?php

namespace App\Http\Controllers\Customers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerMedia;
use App\Http\Controllers\Controller;
use App\Models\CustomerBusinessProfile;
use Illuminate\Support\Facades\Storage;

class CustomerMediaController extends Controller
{
    // Plain view returns (no JSON wrapper)
    public function index($id)
    {
        return view('Admin.Customers.CustomerProfile.AddMedias', ['id' => $id]);
    }

    public function manage($id)
    {
        return view('Admin.Customers.CustomerProfile.ManageMedia', ['id' => $id]);
    }

    public function businessMedia($id, $business_id)
    {
        return view('Admin.Customers.CustomerProfile.BusinessMedia', ['id' => $id, 'business_id' => $business_id]);
    }

    // === Individual Media CRUD (profile_type = 'individual') ===

    // Insert (Store) - Individual
    public function storeIndividualMedia(Request $request, $customer_id)
    {
        $customer = Customer::findOrFail($customer_id);

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
        $fileName = ($customer->customer_uid ?? 'customer') . '_' . now()->format('Ymd_His') . '.' . $extension;
        $file->storeAs('CustomerMedia', $fileName, 'public');
        $filePath = "CustomerMedia/{$fileName}";

        $tags = $request->tags ? json_encode(array_values(array_unique(array_map(fn($tag) => trim(strtolower($tag)), $request->tags)))) : json_encode([]);

        $media = CustomerMedia::create([
            'tenant_id'     => $customer->tenant_id,
            'customer_id'     => $customer->id,
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
    public function getIndividualMedias($customer_id)
    {
        Customer::findOrFail($customer_id);

        $medias = CustomerMedia::where('customer_id', $customer_id)
            ->where('profile_type', 'individual')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $medias->map(fn($media) => $this->formatMedia($media))->toArray()
        ]);
    }

    // Details (Show) - Individual
    public function getIndividualMedia($customer_id, $media_id)
    {
        Customer::findOrFail($customer_id);

        $media = CustomerMedia::where('customer_id', $customer_id)
            ->where('id', $media_id)
            ->where('profile_type', 'individual')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatMedia($media)
        ]);
    }

    // Update - Individual
    public function updateIndividualMedia(Request $request, $customer_id, $media_id)
    {
        $customer = Customer::findOrFail($customer_id);

        $media = CustomerMedia::where('customer_id', $customer_id)
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
            $fileName = ($customer->customer_uid ?? 'Customer') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('CustomerMedia', $fileName, 'public');
            $data['file_url'] = "CustomerMedia/{$fileName}";
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
    public function deleteIndividualMedia($customer_id, $media_id)
    {
        Customer::findOrFail($customer_id);

        $media = CustomerMedia::where('customer_id', $customer_id)
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
    public function storeBusinessMedia(Request $request, $customer_id, $business_id)
    {
        $customer = Customer::findOrFail($customer_id);
        $business = CustomerBusinessProfile::where('customer_id', $customer_id)
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
        $fileName = ($customer->customer_uid ?? 'customer') . '_' . now()->format('Ymd_His') . '.' . $extension;
        $file->storeAs('CustomerBusinessMedia', $fileName, 'public');
        $filePath = "CustomerBusinessMedia/{$fileName}";

        $tags = $request->tags ? json_encode(array_values(array_unique(array_map(fn($tag) => trim(strtolower($tag)), $request->tags)))) : json_encode([]);

        $media = CustomerMedia::create([
            'tenant_id'     => $customer->tenant_id,
            'customer_id'     => $customer->id,
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
    public function getBusinessMedias($customer_id, $business_id)
    {
        Customer::findOrFail($customer_id);
        CustomerBusinessProfile::where('customer_id', $customer_id)
            ->where('id', $business_id)
            ->firstOrFail();

        $medias = CustomerMedia::where('customer_id', $customer_id)
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
    public function getBusinessMedia($customer_id, $business_id, $media_id)
    {
        Customer::findOrFail($customer_id);
        CustomerBusinessProfile::where('customer_id', $customer_id)
            ->where('id', $business_id)
            ->firstOrFail();

        $media = CustomerMedia::where('customer_id', $customer_id)
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
    public function updateBusinessMedia(Request $request, $customer_id, $business_id, $media_id)
    {
        $customer = Customer::findOrFail($customer_id);
        CustomerBusinessProfile::where('customer_id', $customer_id)
            ->where('id', $business_id)
            ->firstOrFail();

        $media = CustomerMedia::where('customer_id', $customer_id)
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
            $fileName = ($customer->customer_uid ?? 'customer') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('CustomerBusinessMedia', $fileName, 'public');
            $data['file_url'] = "CustomerBusinessMedia/{$fileName}";
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
    public function deleteBusinessMedia($customer_id, $business_id, $media_id)
    {
        Customer::findOrFail($customer_id);
        CustomerBusinessProfile::where('customer_id', $customer_id)
            ->where('id', $business_id)
            ->firstOrFail();

        $media = CustomerMedia::where('customer_id', $customer_id)
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
            'customer_id'     => $media->customer_id,
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
