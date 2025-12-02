<?php

namespace App\Http\Controllers\FeeManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Education\FeeMaster;

class FeeMasterController extends Controller
{
    public function index()
    {
        return view('Admin.FeeManagement.FeeMaster.index');
    }

    public function paginate(Request $req)
    {
        $query = FeeMaster::query();

        if (!empty($req->search['value'])) {
            $query->where('fee_name', 'like', "%{$req->search['value']}%");
        }

        $total = $query->count();

        $sortable = ['fee_name', 'id'];
        if (!empty($req->sort_column) && in_array($req->sort_column, $sortable)) {
            $query->orderBy($req->sort_column, $req->sort_dir);
        } else {
            $query->orderBy('id', 'DESC');
        }

        $data = $query->skip($req->start)->take($req->length)->get();
        $i = $req->start + 1;

        return response()->json([
            'draw' => $req->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,

            'data' => $data->map(function ($row) use (&$i) {
                return [
                    'DT_RowIndex' => $i++,
                    'fee_name' => $row->fee_name,
                    'fee_type' => $row->fee_type == '1' ? 'Addon' : 'Deduct',
                    'action' => "
                        <button class='btn btn-sm btn-info editFee' data-id='{$row->id}'>Edit</button>
                        <button class='btn btn-sm btn-danger deleteFee' data-id='{$row->id}'>Delete</button>
                    "
                ];
            })
        ]);
    }

    public function feelist(Request $request)
    {
        $feeType = $request->fee_type;

        $query = FeeMaster::query();

        if ($feeType !== null) {
            $query->where('fee_type', $feeType);
        }

        $fees = $query->get();

        return response()->json([
            'status' => true,
            'data' => $fees
        ]);
    }


    public function store(Request $req)
    {
        $req->validate([
            'fee_name' => 'required|string|max:100',
            'fee_type' => 'required|in:0,1'
        ]);

        FeeMaster::create([
            'fee_name' => $req->fee_name,
            'fee_type' => $req->fee_type,
            'status' => '1'
        ]);

        return response()->json(['status' => 'success', 'message' => 'Fee created successfully']);
    }

    public function edit($id)
    {
        return response()->json(['data' => FeeMaster::findOrFail($id)]);
    }

    public function update(Request $req, $id)
    {
        $req->validate([
            'fee_name' => 'required|string|max:100',
            'fee_type' => 'required|in:0,1'
        ]);

        FeeMaster::findOrFail($id)->update([
            'fee_name' => $req->fee_name,
            'fee_type' => $req->fee_type,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Fee updated']);
    }

    public function delete($id)
    {
        FeeMaster::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Fee deleted']);
    }

  
}
