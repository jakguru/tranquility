<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

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
        return view('app.layouts.my.preferences', ['model' => $request->user()]);
    }
}
