<?php

namespace App\Http\Controllers\FeeManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentFeeController extends Controller
{
    public function index()
    {
        return view('Admin.FeeManagement.StudentFee.index');
    }
}
