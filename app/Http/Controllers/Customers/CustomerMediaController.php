<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerMediaController extends Controller
{
    public function index($id){
        return view ('Admin.Customers.CustomerProfile.AddMedias',['id' => $id]);
    }
}
