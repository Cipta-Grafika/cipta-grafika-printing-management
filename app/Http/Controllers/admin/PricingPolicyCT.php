<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PricingPolicyCT extends Controller
{
    public function index()
    {
        return view('admin.pricing-policy.index');
    }

    public function create()
    {
        return view('admin.pricing-policy.create');
    }

    public function edit()
    {
        return view('admin.pricing-policy.edit');
    }
}
