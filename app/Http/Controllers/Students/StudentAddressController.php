<?php

namespace App\Http\Controllers\Students;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentAddress;
use App\Http\Controllers\Controller;

class StudentAddressController extends Controller
{
    public function index($id)
    {
        return view('Admin.Students.StudentProfile.AddAddress', ['id' => $id]);
    }

    public function update(Request $request, $student_id)
    {
        $validated = $request->validate([
            'state'      => 'required|string|max:120',
            'district'   => 'required|string|max:120',
            'city'       => 'required|string|max:120',
            'pincode'    => 'required|digits:6',
            'line_1'     => 'string|max:120',
            'line_2'     => 'string|max:120',
            'landmark'   => 'nullable|string|max:150',
            'label'      => 'nullable|string|max:100',
            'longitude'  => 'nullable|string|max:50',
            'latitude'   => 'nullable|string|max:50',
            'is_primary' => '1'
        ]);
        $validated['is_primary'] = 1;

        $address = StudentAddress::where('student_id', $student_id)->first();

        if ($address) {
            $address->update($validated);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Student address not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Address saved successfully.',
            'data'    => $address->fresh()
        ], 200);
    }

    public function permanentAddress($student_id)
    {
        $address = StudentAddress::where('student_id', $student_id)->where('is_primary', '1')->first();
        if ($address) {
            return response()->json([
                'success' => true,
                'message' => 'Address Fatch successfully.',
                'data'    => $address
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Student address not found',
            ], 404);
        }
    }
}
