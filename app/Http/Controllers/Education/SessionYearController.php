<?php

namespace App\Http\Controllers\Education;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Education\SessionYear;

class SessionYearController extends Controller
{
    public function index()
    {
        return view('Admin.Education.SessionYear.index');
    }

    public function sessionPaginate(Request $request)
    {
        if ($request->ajax()) {
            $query = SessionYear::orderBy('id', 'desc');

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>';
                })
                ->editColumn('start_date', function ($row) {
                    return $row->start_date
                        ? \Carbon\Carbon::parse($row->start_date)->format('d M Y')
                        : '';
                })
                ->editColumn('end_date', function ($row) {
                    return $row->end_date
                        ? \Carbon\Carbon::parse($row->end_date)->format('d M Y')
                        : '';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary editSessionYear" data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger deleteSessionYear" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    ';
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }
        return view('Admin.Education.SessionYear.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:session_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->filled('is_active') ? 1 : 0;
        $data['tenet_id'] = 1;
        $data['emp_id'] = 1;

        $sessionYear = SessionYear::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Session Year Added Successfully',
            'data' => $sessionYear
        ]);
    }

    public function edit($id)
    {
        $data = SessionYear::findOrFail($id);
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function show($id)
    {
        $sessionYear = SessionYear::findOrFail($id);
        return response()->json(['status' => true, 'data' => $sessionYear]);
    }

    public function fatch($id)
    {
        $sessionYear = SessionYear::findOrFail($id);
        return response()->json(['status' => true, 'data' => $sessionYear]);
    }

    public function list(Request $request)
    {
        $query = SessionYear::whereNull('deleted_at');
        $sessionYears = $query->get();
        return response()->json(['status' => true, 'data' => $sessionYears]);
    }

    public function update(Request $request, $id)
    {
        $sessionYear = SessionYear::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:session_years,name,' . $sessionYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->filled('is_active') ? 1 : 0;

        $sessionYear->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Session Year Updated Successfully'
        ]);
    }

    public function destroy($id)
    {
        $sessionYear = SessionYear::findOrFail($id);
        $sessionYear->delete();

        return response()->json([
            'status' => true,
            'message' => 'Session Year Deleted (Soft)'
        ]);
    }

    public function restore($id)
    {
        SessionYear::withTrashed()->find($id)->restore();
        return response()->json(['status' => true, 'message' => 'Session Year Restored']);
    }

    public function fatchList()
    {
        $sessionYears = SessionYear::all();
        return response()->json(['status' => true, 'data' => $sessionYears]);
    }

    public function forceDelete($id)
    {
        SessionYear::withTrashed()->find($id)->forceDelete();
        return response()->json(['status' => true, 'message' => 'Session Year Permanently Deleted']);
    }

    /**
     * 🟢 Fetch all Active Session Years
     */
    public function activeSessions()
    {
        $activeSessions = SessionYear::where('is_active', 1)->get();

        return response()->json([
            'status' => true,
            'message' => 'Active Sessions Fetched Successfully',
            'data' => $activeSessions
        ]);
    }

    /**
     * ⚪ Fetch all Inactive Session Years
     */
    public function inactiveSessions()
    {
        $inactiveSessions = SessionYear::where('is_active', 0)->get();

        return response()->json([
            'status' => true,
            'message' => 'Inactive Sessions Fetched Successfully',
            'data' => $inactiveSessions
        ]);
    }
}
