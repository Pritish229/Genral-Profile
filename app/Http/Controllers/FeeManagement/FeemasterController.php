<?php

namespace App\Http\Controllers\FeeManagement;

use App\Http\Controllers\Controller;
use App\Models\FeeMaster;
use Illuminate\Http\Request;

class FeeMasterController extends Controller
{
    public function index()
    {
        return view('Admin.FeeManagement.FeeMaster.index');
    }

    public function paginate(Request $req)
    {
        $query = FeeMaster::query();

     
        if ($req->search['value']) {
            $search = $req->search['value'];
            $query->where('fee_name', 'LIKE', "%$search%");
        }

        $total = $query->count();
        $sortableColumns = ['fee_name', 'id']; 

        if ($req->sort_column && in_array($req->sort_column, $sortableColumns)) {
            $query->orderBy($req->sort_column, $req->sort_dir);
        } else {
            $query->orderBy('id', 'DESC'); 
        }
        $data = $query->skip($req->start)->take($req->length)->get();

        $startIndex = $req->start + 1;

        return response()->json([
            'draw' => $req->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,

            'data' => $data->map(function ($row) use (&$startIndex) {
                return [
                    'DT_RowIndex' => $startIndex++, 
                    'fee_name' => $row->fee_name,

                    'action' => "
                    <button class='btn btn-sm btn-info editFee' data-id='{$row->id}'>Edit</button>
                    <button class='btn btn-sm btn-danger deleteFee' data-id='{$row->id}'>Delete</button>
                ",
                ];
            })
        ]);
    }

    public function store(Request $req)
    {
        $req->validate([
            'fee_name' => 'required|string|max:100',
        ]);

        FeeMaster::create([
            'fee_name' => $req->fee_name
        ]);

        return response()->json(['status' => 'success', 'message' => 'Fee created successfully!']);
    }

    public function feelist(){
        $data =  FeeMaster::all();
        return response()->json(['data' => $data]);
    }


    public function edit($id)
    {
        return response()->json(['data' => FeeMaster::findOrFail($id)]);
    }


    public function update(Request $req, $id)
    {
        $req->validate([
            'fee_name' => 'required|string|max:100',
        ]);

        FeeMaster::findOrFail($id)->update([
            'fee_name' => $req->fee_name
        ]);

        return response()->json(['status' => 'success', 'message' => 'Fee updated!']);
    }

    public function delete($id)
    {
        FeeMaster::findOrFail($id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Fee deleted!']);
    }
}
