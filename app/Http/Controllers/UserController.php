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
        return view('app.layouts.models.users.view', ['model' => $user]);
    }
}
