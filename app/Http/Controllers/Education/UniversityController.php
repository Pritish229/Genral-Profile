<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Education\University;
use Illuminate\Support\Facades\Storage;
use App\Models\Education\UniversityCollege;

class UniversityController extends Controller
{
    public function index()
    {
        return view('Admin.Education.ManageUniversity.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'org_name'      => 'required|string|max:255',
            'city'          => 'required|string|max:255',
            'district'      => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'phone_no'      => 'required|string|max:20',
            'alternate_no'  => 'nullable|string|max:20',
            'email_id'      => 'required|email|max:255',
            'alt_email_id'  => 'nullable|email|max:255',
            'org_logo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'address'       => 'required|string',
            'website_url'   => 'nullable|url|max:255',
        ]);

        $data = $request->except('org_logo');

        if ($request->hasFile('org_logo')) {
            $data['org_logo'] = $request->file('org_logo')->store('university', 'public');
        }

        University::create($data);

        return response()->json([
            'status' => true,
            'message' => 'University added successfully!'
        ]);
    }

    public function list(Request $request)
    {
        $query = UniversityCollege::with('university');

        // Search
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

        // Skip ordering on columns not in DB
        $skipOrderColumns = ['DT_RowIndex', 'logo', 'action', 'university.org_name'];

        $orderColumnIndex = $request->order[0]['column'];
        $orderDirection = $request->order[0]['dir'];
        $orderColumn = $request->columns[$orderColumnIndex]['data'];

        if (!in_array($orderColumn, $skipOrderColumns)) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        // Pagination
        $colleges = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        // Format data
        $data = [];
        foreach ($colleges as $index => $row) {
            $data[] = [
                'DT_RowIndex' => $request->start + $index + 1,
                'logo' => $row->org_logo
                    ? '<img src="' . asset("storage/" . $row->org_logo) . '" width="40" class="rounded"/>'
                    : '',
                'university' => [
                    'org_name' => $row->university?->org_name ?? 'N/A'
                ],
                'org_name' => $row->org_name,
                'city' => $row->city,
                'district' => $row->district,
                'state' => $row->state,
                'email_id' => $row->email_id,
                'phone_no' => $row->phone_no,
                'action' => '
                <button class="btn btn-sm btn-info editBtn" data-id="' . $row->id . '">Edit</button>
                <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '">Delete</button>
            ',
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ]);
    }


    public function show($id)
    {
        $data = University::findOrFail($id);
        $data->org_logo_url = $data->org_logo ? asset('storage/' . $data->org_logo) : null;
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $univ = University::findOrFail($id);

        $request->validate([
            'org_name'      => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'district'      => 'required|string|max:255',
            'city'          => 'required|string|max:255',
            'email_id'      => 'required|email|max:255',
            'alt_email_id'  => 'nullable|email|max:255',
            'phone_no'      => 'required|string|max:20',
            'alt_phone_no'  => 'nullable|string|max:20',
            'address'       => 'required|string',
            'website_url'   => 'nullable|url|max:255',
            'org_logo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('org_logo');

        if ($request->hasFile('org_logo')) {
            if ($univ->org_logo && Storage::disk('public')->exists($univ->org_logo)) {
                Storage::disk('public')->delete($univ->org_logo);
            }
            $data['org_logo'] = $request->file('org_logo')->store('university', 'public');
        }

        $univ->update($data);

        return response()->json([
            'status' => true,
            'message' => 'University updated successfully!'
        ]);
    }

    public function allUniversities()
    {
        $universities = University::get();
        return response()->json($universities);
    }
}
