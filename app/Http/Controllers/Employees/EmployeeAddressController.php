<?php

namespace App\Http\Controllers\Employees;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\EmployeeAddress;
use App\Http\Controllers\Controller;

class EmployeeAddressController extends Controller
{
    public function index($id)
    {
        return view('Admin.Employees.EmployeeProfile.AddAddress', ['id' => $id]);
    }

    public function getAddresses($employee_id)
    {
        $addresses = EmployeeAddress::where('employee_id', $employee_id)->get();

        return response()->json([
            'success' => true,
            'data'    => $addresses
        ]);
    }

    // Store new address
    public function storeAddress(Request $request, $employee_id)
    {
        $validated = $request->validate([
            'state'      => 'required|string|max:120',
            'district'   => 'required|string|max:120',
            'city'       => 'required|string|max:120',
            'pincode'    => 'required|digits:6',
            'line_1'     => 'nullable|string|max:120',
            'line_2'     => 'nullable|string|max:120',
            'landmark'   => 'nullable|string|max:150',
            'label'      => 'nullable|string|max:100',
            'longitude'  => 'nullable|string|max:50',
            'latitude'   => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);

        $employee = Employee::findOrFail($employee_id);

        $address = EmployeeAddress::create(array_merge($validated, [
            'employee_id' => $employee->id,
            'tenant_id'  => $employee->tenant_id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully',
            'data'    => $address
        ], 201);
    }

    // Update address
    public function updateAddress(Request $request, Employee $employee, employeeAddress $address)
    {
        $validated = $request->validate([
            'state'      => 'required|string|max:120',
            'district'   => 'required|string|max:120',
            'city'       => 'required|string|max:120',
            'pincode'    => 'required|digits:6',
            'line1'     => 'nullable|string|max:120',
            'line2'     => 'nullable|string|max:120',
            'landmark'   => 'nullable|string|max:150',
            'label'      => 'nullable|string|max:100',
            'longitude'  => 'nullable|string|max:50',
            'latitude'   => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data'    => $address
        ]);
    }

    // Delete address
    public function deleteAddress(employee $employee, employeeAddress $address)
    {
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    // Get permanent address
    public function permanentAddress($employee_id)
    {
        $address = EmployeeAddress::where('employee_id', $employee_id)
            ->where('is_primary', 1)
            ->first();

        if ($address) {
            return response()->json([
                'success' => true,
                'message' => 'Address fetched successfully',
                'data'    => $address
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No primary address found'
        ], 404);
    }
}
