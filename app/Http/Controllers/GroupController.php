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

    public function add(Request $request)
    {
        if (!$request->user()->can('add', Group::class)) {
            abort(403);
        }
        $group = new Group;
        return view('app.layouts.models.groups.edit', ['model' => $group]);
    }

    public function create(Request $request)
    {
        if (!$request->user()->can('add', Group::class)) {
            abort(403);
        }
        echo '<pre>';
        print_r($request->all());
        echo '</pre>';
        exit();
    }

    public function view(Request $request, $id)
    {
        $group = Group::find($id);
        if (!is_a($group, '\App\Group')) {
            abort(404);
        }
        if (!$request->user()->can('view', $group)) {
            abort(403);
        }
        return view('app.layouts.models.groups.edit', ['model' => $group]);
    }

    public function edit(Request $request, $id)
    {
        $group = Group::find($id);
        if (!is_a($group, '\App\Group')) {
            abort(404);
        }
        if (!$request->user()->can('edit', $group)) {
            abort(403);
        }
        echo '<pre>';
        print_r($request->all());
        echo '</pre>';
        exit();
    }
}
