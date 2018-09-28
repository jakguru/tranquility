<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Group;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        if (!$request->user()->can('list', Group::class)) {
            abort(403);
        }
        $listHelper = new \App\Helpers\ModelListHelper(Group::class, $request);
        if ($request->wantsJson()) {
            return \App\Helpers\AjaxFeedbackHelper::success($listHelper->getAJAXReturn(), 'Generated List Successfully');
        }
        return view('app.layouts.modellist', $listHelper->getViewVariables());
    }
}
