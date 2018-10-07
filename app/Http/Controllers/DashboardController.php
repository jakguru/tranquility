<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('app.layouts.dashboard');
    }

    /**
     * Show search results.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $results = \App\Helpers\SearchHelper::search($request->query('s'), $request->query('model', []));
        return view('app.layouts.search', ['results' => $results]);
    }
}
