<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentBasicController extends Controller
{
    public function index($id){
        return view ('Admin.students.AddAddress' , ['student_id' , $id]);
    }
}
