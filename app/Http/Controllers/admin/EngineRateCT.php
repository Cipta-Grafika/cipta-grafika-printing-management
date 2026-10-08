<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EngineRateCT extends Controller
{
    public function index()
    {
        return view('admin.engine-rate.index');
    }

    public function create()
    {
        return view('admin.engine-rate.create');
    }

    public function edit()
    {
        return view('admin.engine-rate.edit');
    }
}
