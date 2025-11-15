<?php

namespace App\Http\Controllers\FeeManagement;

use Illuminate\Http\Request;
use App\Models\Education\CourseFee;
use App\Models\Education\FeeMaster;
use App\Http\Controllers\Controller;

class CourseFeeController extends Controller
{
    public function index()
    {
        return view('Admin.FeeManagement.CourseFees.index');
    }

    public function paginate(Request $req)
    {
        $baseQuery = CourseFee::query(); // for total count (no filters)
        $total = $baseQuery->count();

        $query = CourseFee::with(['sessionYear', 'course', 'courseClass', 'feeMaster']);

        // Filters
        if ($req->session_year_id) {
            $query->where('session_year_id', $req->session_year_id);
        }
        if ($req->course_id) {
            $query->where('course_id', $req->course_id);
        }
        if ($req->course_class_id) {
            $query->where('course_class_id', $req->course_class_id);
        }

        // Search
        if ($req->has('search') && !empty($req->search['value'])) {
            $search = $req->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('fee_name', 'LIKE', "%$search%")
                    ->orWhere('fee_amount', 'LIKE', "%$search%")
                    ->orWhere('total_fee', 'LIKE', "%$search%");
            });
        }

        $filtered = $query->count(); // count AFTER filters

        // Sorting
        $sortable = ['id', 'fee_amount', 'total_fee', 'feestype'];
        if ($req->sort_column && in_array($req->sort_column, $sortable)) {
            $query->orderBy($req->sort_column, $req->sort_dir);
        } else {
            $query->orderBy('id', 'DESC');
        }

        // Pagination
        $data = $query->skip($req->start)->take($req->length)->get();
        $startIndex = $req->start + 1;

        return response()->json([
            'draw' => $req->draw,
            'recordsTotal' => $total,        // fixed
            'recordsFiltered' => $filtered,  // fixed

            'data' => $data->map(function ($row) use (&$startIndex) {

                $status = $row->status == 1
                    ? "<span class='badge bg-success'>Active</span>"
                    : "<span class='badge bg-danger'>Inactive</span>";

                return [
                    'DT_RowIndex' => $startIndex++,
                    'session' => $row->sessionYear->name ?? '-',
                    'course' => $row->course->course_name ?? '-',
                    'class' => $row->courseClass->class_name ?? '-',
                    'fee_name' => $row->fee_name ?? '-',

                    // FIXED: convert enum number → readable text
                    'feestype' => [
                        '1' => 'Annual',
                        '2' => 'Monthly',
                        '3' => 'Other',
                    ][$row->feestype] ?? 'N/A',

                    'fee_amount' => $row->fee_amount,
                    'total_fee' => $row->total_fee,
                    'status' => $status,

                    'action' => "
                    <button class='btn btn-sm btn-info editCourseFee' data-id='{$row->id}'>Edit</button>
                    <button class='btn btn-sm btn-danger deleteCourseFee' data-id='{$row->id}'>Delete</button>
                    ",
                ];
            })
        ]);
    }


    public function store(Request $req)
    {
        $req->validate([
            'session_year_id' => 'required',
            'course_id' => 'required',
            'course_class_id' => 'required',
            'fee_master_id' => 'required|exists:fee_masters,id',
            'fee_amount' => 'required|numeric',
            'feestype' => 'required|in:0,1,2',
            'status' => 'required|in:0,1',
        ]);

        // Fetch fee name from master
        $feeMaster = FeeMaster::find($req->fee_master_id);
        $feeName = $feeMaster->fee_name;

        // Auto calculate times & total
        $times = match ($req->feestype) {
            '1' => 1,   // Annual
            '2' => 12,  // Monthly
            '3' => 1,   // Other
            default => 1,
        };

        $total = $times * $req->fee_amount;
        $fee_name = FeeMaster::where('id', $req->fee_master_id)->first();
        CourseFee::create([
            'session_year_id' => $req->session_year_id,
            'course_id' => $req->course_id,
            'course_class_id' => $req->course_class_id,
            'fee_master_id' => $req->fee_master_id,
            'fee_name' => FeeMaster::find($req->fee_master_id)->fee_name,
            'times_in_year' => $times,
            'fee_amount' => $req->fee_amount,
            'total_fee' => $total,
            'feestype' => $req->feestype,
            'status' => $req->status,
            'is_active' => $req->status == "1" ? 1 : 0,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Course Fee Added Successfully!']);
    }

    public function edit($id)
    {
        return response()->json([
            'data' => CourseFee::findOrFail($id)
        ]);
    }

    public function update(Request $req, $id)
    {
        $req->validate([
            'fee_amount' => 'required|numeric',
            'feestype' => 'required|in:0,1,2',
            'status' => 'required|in:0,1',
        ]);

        $row = CourseFee::findOrFail($id);

        // Auto calculate
        $times = match ($req->feestype) {
            '1' => 1,
            '2' => 12,
            '3' => 1,
        };

        $total = $times * $req->fee_amount;

        $row->update([
            'times_in_year' => $times,
            'fee_amount' => $req->fee_amount,
            'total_fee' => $total,
            'feestype' => $req->feestype,
            'status' => $req->status,
            'is_active' => $req->status === "1" ? 1 : 0,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Course Fee Updated!']);
    }

    public function delete($id)
    {
        CourseFee::findOrFail($id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Course Fee Deleted!']);
    }
}
