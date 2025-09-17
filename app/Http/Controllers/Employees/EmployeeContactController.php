<?php

namespace App\Http\Controllers\Employees;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\EmployeeContact;
use App\Http\Controllers\Controller;

class EmployeeContactController extends Controller
{

    public function index($id)
    {
        return view('Admin.Employees.EmployeeProfile.AddContact', ['id' => $id]);
    }

    public function manageContact($id)
    {
        return view('Admin.Employees.EmployeeProfile.ManageContact', ['id' => $id]);
    }
    public function storeContact(Request $request, $employee_id)
    {
        // Validate request
        $validated = $request->validate([
            'contact_type' => 'required|string|max:120',
            'value'        => 'required|string|max:120',
            'label'        => 'nullable|string|max:120',
        ]);

        $employee = Employee::findOrFail($employee_id);

        $contact = EmployeeContact::create([
            'contact_type' => $validated['contact_type'],
            'label'        => $validated['label'] ?? null,
            'value'        => $validated['value'],
            'employee_id'   => $employee->id,
            'tenant_id'    => $employee->tenant_id,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $contact
        ], 201);
    }

    public function getContacts($id)
    {
        $employeecontact = EmployeeContact::where('employee_id', $id)->get();
        return response()->json([
            'success' => true,
            'data' => $employeecontact
        ]);
    }



    // ✅ Update existing contact
    public function updateContact(Request $request, employee $employee, EmployeeContact $contact)
    {
        if ($contact->employee_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'This contact does not belong to the employee.'
            ], 403);
        }

        $validated = $request->validate([
            'contact_type' => 'required|string|max:120',
            'value'        => 'required|string|max:120',
            'label'        => 'nullable|string|max:120',
        ]);

        $contact->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $contact
        ]);
    }

    // ✅ Delete (Soft Delete)
    public function deleteContact(Employee $employee, EmployeeContact $contact)
    {
        if ($contact->is_primary) {
            return response()->json([
                'success' => false,
                'message' => 'Primary contact cannot be deleted.'
            ], 403);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully.'
        ]);
    }

    public function permanentContact($employee_id)
    {
        $contact = EmployeeContact::where('employee_id', $employee_id)->where('is_primary', '1')->first();
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
