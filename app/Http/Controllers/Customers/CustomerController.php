<?php

namespace App\Http\Controllers\Customers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerContact;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\CustomerIndividualProfile;

class CustomerController extends Controller
{
    public function create()
    {
        return view('Admin.Customers.CustomerProfile.AddCustomer');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'               => 'required|in:individual,business',
            'primary_email'      => 'required|email|unique:customers,primary_email',
            'primary_phone'      => 'nullable|string|max:20',
            'first_name'         => 'required_if:type,individual|string|max:100',
            'middle_name'        => 'nullable|string|max:100',
            'last_name'          => 'required_if:type,individual|string|max:100',
            'dob'                => 'nullable|date',
            'gender'             => 'nullable|in:male,female,other',
            'marital_status'     => 'nullable|in:single,married,divorced,widowed,other',
            'preferred_currency' => 'nullable|string|max:3',
            'customer_uid'         => 'nullable|string|unique:customers,customer_uid',
            'onboarding_channel' => 'nullable|string|max:255',
            'occupation'         => 'nullable|string|max:255',
            'nationality'        => 'nullable|string|max:100',
            'preferred_language' => 'nullable|string|max:100',
            'preferred_currency' => 'nullable|string|max:3',
        ]);

        $customer = Customer::create([
            'customer_uid'    => $validated['customer_uid'],
            'tenant_id'     => '1',
            'type'          => $validated['type'],
            'primary_email' => $validated['primary_email'],
            'primary_phone' => $validated['primary_phone'] ?? null,
        ]);

        if ($customer) {
            CustomerContact::create([
                'customer_id'    => $customer->id,
                'tenant_id'    => '1',
                'contact_type' => 'phone',
                'value'        => $validated['primary_phone'] ?? null,
                'is_primary'   => true,
            ]);

            CustomerContact::create([
                'customer_id'    => $customer->id,
                'tenant_id'    => '1',
                'contact_type' => 'email',
                'value'        => $validated['primary_email'],
                'is_primary'   => true,
            ]);
        }

        // Handle individual profile
        $profile = CustomerIndividualProfile::create([
            'customer_id'          => $customer->id,
            'tenant_id'          => '1',
            'first_name'         => $validated['first_name'],
            'middle_name'        => $validated['middle_name'] ?? null,
            'last_name'          => $validated['last_name'],
            'type'               => $customer->type,
            'dob'                => $validated['dob'] ?? null,
            'gender'             => $validated['gender'] ?? null,
            'marital_status'     => $validated['marital_status'] ?? null,
            'occupation'         => $validated['occupation'] ?? null,
            'nationality'        => $validated['nationality'] ?? null,
            'preferred_language' => $validated['preferred_language'] ?? null,
            'preferred_currency' => $validated['preferred_currency'] ?? null,
        ]);


        // Handle avatar upload only for individuals
        if ($request->hasFile('avatar_url')) {
            $file = $request->file('avatar_url');
            $extension = $file->getClientOriginalExtension();
            $fileName = ($validated['customer_uid'] ?? 'customer') . '_' . now()->format('Ymd_His') . '.' . $extension;
            $file->storeAs('customerImages', $fileName, 'public');

            $profile->update([
                'avatar_url' => "customerImages/{$fileName}",
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'customer created successfully',
            'data'    => $customer,
        ]);
    }

    public function manage($id)
    {
        return view('Admin.customers.customerProfile.Managecustomer', [
            'id' => $id
        ]);
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        // Only get individual profile for this management page
        $profile = CustomerIndividualProfile::where('customer_id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => $customer,
                'profile' => $profile
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'primary_email'      => 'nullable|email|unique:customers,primary_email,' . $id,
            'primary_phone'      => 'nullable|string|max:20',
            'first_name'         => 'required|string|max:100',
            'middle_name'        => 'nullable|string|max:100',
            'last_name'          => 'required|string|max:100',
            'dob'                => 'nullable|date',
            'gender'             => 'nullable|in:male,female,other,unspecified',
            'marital_status'     => 'nullable|in:single,married,divorced,widowed,other',
            'customer_uid'         => 'nullable|string|unique:customers,customer_uid,' . $id,
            'onboarding_channel' => 'nullable|string|max:255',
            'occupation'         => 'nullable|string|max:255',
            'nationality'        => 'nullable|string|max:100',
            'preferred_language' => 'nullable|string|max:100',
            'preferred_currency' => 'nullable|string|max:3',
            'status'             => 'nullable|in:active,inactive,suspended',
            'notes'              => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
    
            // Update customer record
            $customer->update([
                'customer_uid'         => $validated['customer_uid'] ?? $customer->customer_uid,
                'onboarding_channel' => $validated['onboarding_channel'] ?? $customer->onboarding_channel,
                'status'             => $validated['status'] ?? $customer->status,
                'notes'              => $validated['notes'] ?? $customer->notes,
            ]);
            $profile = CustomerIndividualProfile::firstOrCreate(
                ['customer_id' => $id],
                [
                    'tenant_id' => $customer->tenant_id,
                ]
            );
            $profile->update([
                'first_name'         => $validated['first_name'],
                'middle_name'        => $validated['middle_name'],
                'last_name'          => $validated['last_name'],
                'dob'                => $validated['dob'],
                'gender'             => $validated['gender'],
                'marital_status'     => $validated['marital_status'],
                'occupation'         => $validated['occupation'],
                'nationality'        => $validated['nationality'],
                'preferred_language' => $validated['preferred_language'],
                'preferred_currency' => $validated['preferred_currency'],
            ]);

            // Handle avatar upload
            if ($request->hasFile('avatar_url')) {
                // Delete old avatar if exists
                if ($profile->avatar_url && Storage::disk('public')->exists($profile->avatar_url)) {
                    Storage::disk('public')->delete($profile->avatar_url);
                }

                $file = $request->file('avatar_url');
                $extension = $file->getClientOriginalExtension();
                $fileName = ($customer->customer_uid ?? 'customer') . '_' . now()->format('Ymd_His') . '.' . $extension;
                $file->storeAs('customerImages', $fileName, 'public');

                $profile->update([
                    'avatar_url' => "customerImages/{$fileName}",
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'customer updated successfully',
                'data' => $customer->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update Customer: ' . $e->getMessage()
            ], 500);
        }
    }

}
