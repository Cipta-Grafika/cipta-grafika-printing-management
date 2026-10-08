<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PricingCT extends Controller
{
    public function index()
    {
        return view('admin.pricing.index');
    }

    public function create()
    {
        return view('admin.pricing.create');
    }

    public function edit()
    {
        return view('admin.pricing.edit');
    }
}
