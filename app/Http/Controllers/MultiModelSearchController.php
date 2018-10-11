<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Helpers\AjaxFeedbackHelper;
use \App\Helpers\PermissionsHelper;
use \App\Helpers\SearchHelper;
use \App\Helpers\ModelListHelper;

class MultiModelSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $return = [];
        if (strlen($request->input('search')) > 0) {
            $modelsToSearch = PermissionsHelper::getModelsWithTrait('Receivable');
            $results = SearchHelper::search($request->input('search'), $modelsToSearch);
            foreach ($results as $model) {
                $ret = new \stdClass();
                $ret->value = $model->id;
                $ret->display = $model->getDisplay();
                $ret->type = ModelListHelper::getSingleLabelForClass($model);
                array_push($return, $ret);
            }
        }
        return AjaxFeedbackHelper::successResponse($return, __('Retrieved Search Results Successfully'));
    }
}
