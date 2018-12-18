<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserAddressRequest;

class UserAddressController extends Controller
{
    public function index(){
        
        return view('user_address.index', ['addresses' => auth()->user()->address]);
    }

    public function create(){
        return view('user_address.create');
    }

    public function store(UserAddressRequest $request)
    {
        $request->user()->address()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone'
        ]));

        return redirect()->route('user_address.index');
    }
}
