<?php

namespace App\Http\Controllers\Admin;

use App\Models\ItemBasicInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dashBoardPage()
    {
        return view('Admin.Dashboard.Home');
    }

}
