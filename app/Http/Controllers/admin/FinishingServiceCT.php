<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinishingServiceCT extends Controller
{
    public function index()
    {
        return view('admin.finishing-service.index');
    }

    public function create()
    {
        return view('admin.finishing-service.create');
    }

    public function edit()
    {
        return view('admin.finishing-service.edit');
    }
}
