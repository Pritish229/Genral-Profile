<?php

namespace App\Http\Controllers\FeeManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeeSchduleController extends Controller
{
    public function index()
    {
        return view('Admin.FeeManagement.StudentFee.FeeSchdule');
    }
}
