<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        if (!$request->user()->can('list', User::class)) {
            abort(403);
        }
        $listHelper = new \App\Helpers\ModelListHelper(User::class, $request);
        if ($request->wantsJson()) {
            return \App\Helpers\AjaxFeedbackHelper::success($listHelper->getAJAXReturn(), 'Generated List Successfully');
        }
        return view('app.layouts.modellist', $listHelper->getViewVariables());
    }

    public function view(Request $request, $id)
    {
        $user = User::find($id);
        if (!is_a($user, '\App\User')) {
            abort(404);
        }
        if (!$request->user()->can('view', $user)) {
            abort(403);
        }
        return view('app.layouts.models.users.view', ['model' => $user]);
    }

    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        if (!is_a($user, '\App\User')) {
            abort(404);
        }
        if (!$request->user()->can('edit', $user)) {
            abort(403);
        }
        return view('app.layouts.models.users.edit', ['model' => $user]);
    }

    public function audit(Request $request, $id)
    {
        $user = User::find($id);
        if (!is_a($user, '\App\User')) {
            abort(404);
        }
        if (!$request->user()->can('view', $user)) {
            abort(403);
        }
        $total_count = $user->activities()->count();
        $page = intval($request->input('page', 1));
        if ($page < 1) {
            $page = 1;
        }
        $total_pages = (ceil($total_count / config('app.listsize')));
        return view('app.layouts.models.shared.audit', [
            'model' => $user,
            'total_activities' => $total_count,
            'page' => $page,
            'total_pages' => $total_pages,
            'activities' => $user->activities()->limit(config('app.listsize'))->offset(($page - 1) * config('app.listsize'))->latest()->get(),
            'next_page' => ($page < ceil($total_count / config('app.listsize')) ) ? $page + 1 : 0,
            'previous_page' => ($page > 1) ? $page - 1 : 0,
        ]);
    }
}
