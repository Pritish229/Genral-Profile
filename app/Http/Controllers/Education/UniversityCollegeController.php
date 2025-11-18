<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Education\University;
use Illuminate\Support\Facades\Storage;
use App\Models\Education\UniversityCollege;

class UniversityCollegeController extends Controller
{
    public function index()
    {
        return view('Admin.Education.ManageCollege.index');
    }

    public function list(Request $request)
    {
        $query = UniversityCollege::with('university');

        if ($request->search['value']) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('org_name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%")
                    ->orWhere('email_id', 'like', "%{$search}%")
                    ->orWhere('phone_no', 'like', "%{$search}%");
            });
        }

        $total = UniversityCollege::count();
        $filtered = $query->count();

        $columnMap = [
            0 => null,
            1 => null,
            2 => 'university_id',
            3 => 'org_name',
            4 => 'city',
            5 => 'district',
            6 => 'state',
            7 => 'email_id',
            8 => 'phone_no',
            9 => null
        ];

        $orderColumnIndex = $request->order[0]['column'];
        $orderDirection = $request->order[0]['dir'];
        $orderColumn = $columnMap[$orderColumnIndex] ?? null;

        if ($orderColumn) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        $colleges = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = [];
        foreach ($colleges as $index => $row) {
            $data[] = [
                'DT_RowIndex' => $request->start + $index + 1,
                'logo' => $row->org_logo
                    ? '<img src="' . asset("storage/" . $row->org_logo) . '" width="40" class="rounded"/>'
                    : '',
                'university_name' => $row->university?->org_name ?? 'N/A',
                'org_name' => $row->org_name,
                'city' => $row->city,
                'district' => $row->district,
                'state' => $row->state,
                'email_id' => $row->email_id,
                'phone_no' => $row->phone_no,
                'action' =>
                '<button class="btn btn-sm btn-info editBtn" data-id="' . $row->id . '">Edit</button>
                     <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '">Delete</button>',
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ]);
    }

    public function listAll()
    {
        $colleges = UniversityCollege::with('university')->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $colleges
        ]);
    }

    public function create()
    {
        $universities = University::where('is_active', true)->get();

        return response()->json([
            'status' => true,
            'universities' => $universities
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'university_id' => 'required|exists:universities,id',
            'org_name' => 'required|string|max:255',
            'city' => 'required|string',
            'district' => 'required|string',
            'state' => 'required|string',
            'phone_no' => 'required|unique:university_colleges,phone_no',
            'email_id' => 'required|email|unique:university_colleges,email_id',
            'alt_email_id' => 'nullable|email',
            'org_logo' => 'nullable|image|max:2048',
            'address' => 'required|string',
            'website_url' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('org_logo')) {
            $data['org_logo'] = $request->file('org_logo')->store('logos', 'public');
        }

        $college = UniversityCollege::create($data);

        return response()->json([
            'status' => true,
            'message' => 'College created successfully.',
            'data' => $college
        ]);
    }

    public function edit(UniversityCollege $college)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'college' => [
                    'id' => $college->id,
                    'org_name' => $college->org_name,
                    'city' => $college->city,
                    'district' => $college->district,
                    'state' => $college->state,
                    'email_id' => $college->email_id,
                    'phone_no' => $college->phone_no,
                    'address' => $college->address,
                    'website_url' => $college->website_url,
                    'org_logo' => $college->org_logo ? asset('storage/' . $college->org_logo) : null
                ],
                'university_name' => $college->university->org_name,
                'university_id' => $college->university->id
            ]
        ]);
    }

    public function update(Request $request, UniversityCollege $college)
    {
        $request->validate([
            'org_name'      => 'required|string|max:255',
            'city'          => 'required|string',
            'district'      => 'required|string',
            'state'         => 'required|string',
            'phone_no'      => "required|unique:university_colleges,phone_no,{$college->id}",
            'email_id'      => "required|email|unique:university_colleges,email_id,{$college->id}",
            'alt_email_id'  => 'nullable|email',
            'org_logo'      => 'nullable|image|max:2048',
            'address'       => 'required|string',
            'website_url'   => 'nullable|string',
        ]);

        $data = $request->all();
        $data['university_id'] = $college->university_id;

        if ($request->hasFile('org_logo')) {

            if ($college->org_logo && Storage::disk('public')->exists($college->org_logo)) {
                Storage::disk('public')->delete($college->org_logo);
            }

            $data['org_logo'] = $request->file('org_logo')->store('logos', 'public');
        }

        $college->update($data);

        return response()->json([
            'status' => true,
            'message' => 'College updated successfully.',
            'data' => $college
        ]);
    }

    public function destroy(UniversityCollege $college)
    {
        $college->delete();

        return response()->json([
            'status' => true,
            'message' => 'College deleted successfully.',
        ]);
    }
}
