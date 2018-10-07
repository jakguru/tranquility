<?php

namespace App\Helpers;

use \App\Helpers\ModelListHelper;
use \App\Helpers\ModelImageHelper;
use \App\Helpers\PermissionsHelper;
use \App\Helpers\WeatherHelper;

class BusinessCardHelper
{
    public static function getSingleLabelForClass($model)
    {
        return ModelListHelper::getSingleLabelForClass($model);
    }

    public static function getUrlForBackgroundImage($model, $model_id)
    {
        return ModelImageHelper::getUrlForBackgroundImage($model, $model_id);
    }

    public static function getUrlForAvatarImage($model, $model_id)
    {
        return ModelImageHelper::getUrlForAvatarImage($model, $model_id);
    }

    public static function getViewRoute($model)
    {
        return sprintf('view-%s', strtolower(self::getSingleLabelForClass($model)));
    }

    public static function getEditRoute($model)
    {
        return sprintf('edit-%s', strtolower(self::getSingleLabelForClass($model)));
    }

    public static function getAuditRoute($model)
    {
        return sprintf('audit-%s', strtolower(self::getSingleLabelForClass($model)));
    }

    public static function hasLog($model)
    {
        return PermissionsHelper::modelHasTrait($model, 'Loggable');
    }

    public static function isOwned($model)
    {
        return PermissionsHelper::modelHasTrait($model, 'Ownable');
    }

    public static function formatModelAddress($model)
    {
        $address_array = [];
        if (!is_null($model->address_line_1) && strlen($model->address_line_1) > 0) {
            array_push($address_array, $model->address_line_1);
        }
        if (!is_null($model->address_line_2) && strlen($model->address_line_2) > 0) {
            array_push($address_array, $model->address_line_2);
        }
        $address_line_3_array = [];
        if (!is_null($model->city) && strlen($model->city) > 0) {
            array_push($address_line_3_array, $model->city);
        }
        if (!is_null($model->state) && strlen($model->state) > 0) {
            array_push($address_line_3_array, $model->state);
        }
        if (!is_null($model->postal) && strlen($model->postal) > 0) {
            array_push($address_line_3_array, $model->postal);
        }
        if (count($address_line_3_array) > 0) {
            array_push($address_array, implode(', ', $address_line_3_array));
        }
        if (!is_null($model->country) && strlen($model->country) > 0 && 'XX' !== $model->country) {
            array_push($address_array, CountryHelper::getCountryName($model->country));
        }
        if (count($address_array) > 0) {
            return implode("\n", $address_array);
        } else {
            return __('No Address Information');
        }
    }

    public static function getWeatherForModel($model)
    {
        $city = (!is_null($model->city)) ? $model->city : 'Nordfjord';
        $country = (!is_null($model->country) && 'XX' !== $model->country) ? $model->country : 'NO';
        $weather = new WeatherHelper($city, $country);
        return $weather->getConsensus();
    }
}
