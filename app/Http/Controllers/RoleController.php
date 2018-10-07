<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Role;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        if (!$request->user()->can('list', Role::class)) {
            abort(403);
        }
        $listHelper = new \App\Helpers\HierarchiedModelListHelper(Role::class, $request);
        if ($request->wantsJson()) {
            return \App\Helpers\AjaxFeedbackHelper::success($listHelper->getAJAXReturn(), 'Generated List Successfully');
        }
        return view('app.layouts.modellist', $listHelper->getViewVariables());
    }

    public function add(Request $request)
    {
        if (!$request->user()->can('add', Role::class)) {
            abort(403);
        }
        $role = new Role;
        return view('app.layouts.models.roles.edit', ['model' => $role]);
    }

    public function create(Request $request)
    {
        if (!$request->user()->can('add', Role::class)) {
            abort(403);
        }
        Validator::make($request->all(), [
            'name' => 'required|string',
            'role_id' => ['nullable','exists:roles,id'],
        ])->validate();
        $role = new Role;
        $role->name = $request->input('name');
        $role->role_id = $request->input('role_id');
        $role->save();
        return Redirect::route('edit-role', ['id' => $role->id])->with('globalsuccessmessage', __('Created Role Successfully'));
    }

    public function view(Request $request, $id)
    {
        $role = Role::find($id);
        if (!is_a($role, '\App\Role')) {
            abort(404);
        }
        if (!$request->user()->can('view', $role)) {
            abort(403);
        }
        return view('app.layouts.models.roles.edit', ['model' => $role]);
    }

    public function audit(Request $request, $id)
    {
        $role = Role::find($id);
        if (!is_a($role, '\App\Role')) {
            abort(404);
        }
        if (!$request->user()->can('view', $role)) {
            abort(403);
        }
        $total_count = $role->activities()->count();
        $page = intval($request->input('page', 1));
        if ($page < 1) {
            $page = 1;
        }
        $total_pages = (ceil($total_count / config('app.listsize')));
        return view('app.layouts.models.shared.audit', [
            'model' => $role,
            'total_activities' => $total_count,
            'page' => $page,
            'total_pages' => $total_pages,
            'activities' => $role->activities()->limit(config('app.listsize'))->offset(($page - 1) * config('app.listsize'))->latest()->get(),
            'next_page' => ($page < ceil($total_count / config('app.listsize')) ) ? $page + 1 : 0,
            'previous_page' => ($page > 1) ? $page - 1 : 0,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $role = Role::find($id);
        if (!is_a($role, '\App\Role')) {
            abort(404);
        }
        if (!$request->user()->can('edit', $role)) {
            abort(403);
        }
        Validator::make($request->all(), [
            'name' => 'required|string',
            'role_id' => ['nullable','exists:roles,id', Rule::notIn([$role->id]), new \App\Rules\RoleCannotBeItsOwnChild($role)],
        ])->validate();
        $role->name = $request->input('name');
        $role->role_id = $request->input('role_id');
        $role->save();
        return Redirect::route('edit-role', ['id' => $role->id])->with('globalsuccessmessage', __('Updated Role Successfully'));
    }
}
