<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Education\University;
use Illuminate\Support\Facades\Storage;

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
        if ($request->ajax()) {
            $data = University::orderBy('id', 'DESC');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('logo', function ($row) {
                    $url = $row->org_logo
                        ? asset('storage/' . $row->org_logo)
                        : asset('no-image.png');
                    return '<img src="' . $url . '" width="50" height="50" class="rounded">';
                })
                ->addColumn('action', function ($row) {
                    $manageUrl = route('education.universitycourse.index', $row->id);
                    return '
                    <button class="btn btn-sm btn-primary editBtn" data-id="' . $row->id . '">Edit</button>
                    <a href="' . $manageUrl . '" class="btn btn-sm btn-success">Manage Course</a>';
                })
                ->rawColumns(['logo', 'action'])
                ->make(true);
        }
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
}
