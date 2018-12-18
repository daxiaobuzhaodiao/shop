<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(){
        return view('user_address.index', ['address' => auth()->user()->address]);
    }
}
