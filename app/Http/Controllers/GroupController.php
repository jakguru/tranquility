<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Group;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

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
        $group = new Group;
        $request->merge(['sudo' => $request->has('sudo')]);
        $request->merge(['infosec' => $request->has('infosec')]);
        $rules = [
            'name' => 'required|string',
            'ip_whitelist' => ['required', 'string', new \App\Rules\IPWhiteList],
            'sudo' => 'boolean',
            'infosec' => 'boolean',
        ];
        foreach (\App\Helpers\PermissionsHelper::getPermitableModels() as $class) {
            foreach (\App\Helpers\PermissionsHelper::getPermissionFieldsForModel(str_after($class, '\\App\\')) as $permission) {
                $rules[$permission] = ['required', 'string', Rule::in(\App\Helpers\PermissionsHelper::$permissionOptions)];
            }
        }
        Validator::make($request->all(), $rules)->validate();
        foreach ($rules as $key => $rule) {
            $group->{$key} = $request->input($key);
        }
        $group->save();
        return Redirect::route('edit-group', ['id' => $group->id])->with('globalsuccessmessage', __('Updated Settings Successfully'));
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

    public function audit(Request $request, $id)
    {
        $group = Group::find($id);
        if (!is_a($group, '\App\Group')) {
            abort(404);
        }
        if (!$request->user()->can('view', $group)) {
            abort(403);
        }
        $total_count = $group->activities()->count();
        $page = intval($request->input('page', 1));
        if ($page < 1) {
            $page = 1;
        }
        $total_pages = (ceil($total_count / config('app.listsize')));
        return view('app.layouts.models.shared.audit', [
            'model' => $group,
            'total_activities' => $total_count,
            'page' => $page,
            'total_pages' => $total_pages,
            'activities' => $group->activities()->limit(config('app.listsize'))->offset(($page - 1) * config('app.listsize'))->latest()->get(),
            'next_page' => ($page < ceil($total_count / config('app.listsize')) ) ? $page + 1 : 0,
            'previous_page' => ($page > 1) ? $page - 1 : 0,
        ]);
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
        $section = $request->input('section');
        switch ($section) {
            case 'users':
                $users = $request->has('users') ? array_keys($request->input('users')) : [];
                Validator::make(['users' => $users], [
                    'users' => 'array|nullable',
                    'users.*' => 'numeric|exists:users,id',
                ])->validate();
                $ownusers = $group->users->pluck('id')->toArray();
                $users_to_add = [];
                $users_to_remove = [];
                foreach ($users as $user_id) {
                    if (!in_array($user_id, $ownusers)) {
                        array_push($users_to_add, $user_id);
                    }
                }
                foreach ($ownusers as $user_id) {
                    if (!in_array($user_id, $users)) {
                        array_push($users_to_remove, $user_id);
                    }
                }
                foreach ($users_to_add as $user_id) {
                    $group->users()->attach($user_id);
                }
                foreach ($users_to_remove as $user_id) {
                    $group->users()->detach($user_id);
                }
                $group->save();
                return Redirect::route('edit-group', ['id' => $group->id])->with('globalsuccessmessage', __('Updated Associated Users Successfully'));
                break;

            case 'settings':
                $request->merge(['sudo' => $request->has('sudo')]);
                $request->merge(['infosec' => $request->has('infosec')]);
                $rules = [
                    'name' => 'required|string',
                    'ip_whitelist' => ['required', 'string', new \App\Rules\IPWhiteList],
                    'sudo' => 'boolean',
                    'infosec' => 'boolean',
                ];
                foreach (\App\Helpers\PermissionsHelper::getPermitableModels() as $class) {
                    foreach (\App\Helpers\PermissionsHelper::getPermissionFieldsForModel(str_after($class, '\\App\\')) as $permission) {
                        $rules[$permission] = ['required', 'string', Rule::in(\App\Helpers\PermissionsHelper::$permissionOptions)];
                    }
                }
                Validator::make($request->all(), $rules)->validate();
                foreach ($rules as $key => $rule) {
                    $group->{$key} = $request->input($key);
                }
                $group->save();
                return Redirect::route('edit-group', ['id' => $group->id])->with('globalsuccessmessage', __('Updated Settings Successfully'));
                break;
            
            default:
                return Redirect::route('edit-group', ['id' => $group->id])->with('globalerrormessage', sprintf(__('Unknown Section "%s"'), $section));
                break;
        }
    }
}
