<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function inbox(Request $request)
    {
        abort(501);
    }

    public function calendar(Request $request)
    {
        abort(501);
    }

    public function preferences(Request $request)
    {
        abort(501);
    }
}
