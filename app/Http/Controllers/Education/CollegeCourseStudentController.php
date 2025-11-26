<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CollegeCourseStudentController extends Controller
{
    public function index()
    {
        return view('Admin.Education.CollegeCourse.AddStudent');
    }
}
