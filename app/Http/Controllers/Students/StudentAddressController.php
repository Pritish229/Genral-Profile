<?php

namespace App\Http\Controllers\Students;

use App\Models\Student;
use App\Models\StudentAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentAddressController extends Controller
{
    // Show address form page
    public function index($id)
    {
        return view('Admin.Students.StudentProfile.AddAddress', ['id' => $id]);
    }


    public function manageAddress($id)
    {
        return view('Admin.Students.StudentProfile.ManageAddress', ['id' => $id]);
    }

    // Get all addresses (for datatable/ajax)
    public function getAddresses($student_id)
    {
        $addresses = StudentAddress::where('student_id', $student_id)->get();

        return response()->json([
            'success' => true,
            'data'    => $addresses
        ]);
    }

    // Store new address
    public function storeAddress(Request $request, $student_id)
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

        $student = Student::findOrFail($student_id);

        $address = StudentAddress::create(array_merge($validated, [
            'student_id' => $student->id,
            'tenant_id'  => $student->tenant_id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully',
            'data'    => $address
        ], 201);
    }

    // Update address
    public function updateAddress(Request $request, Student $student, StudentAddress $address)
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

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data'    => $address
        ]);
    }

    // Delete address
    public function deleteAddress(Student $student, StudentAddress $address)
    {
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    // Get permanent address
    public function permanentAddress($student_id)
    {
        $address = StudentAddress::where('student_id', $student_id)
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
