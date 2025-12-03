<?php

namespace App\Http\Controllers\FeeManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentPaymentController extends Controller
{
    public function index()
    {
        return view('Admin.FeeManagement.StudentPayment.index');
    }
}
