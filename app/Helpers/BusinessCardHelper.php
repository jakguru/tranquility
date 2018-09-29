<?php

namespace App\Helpers;

use \App\Helpers\ModelListHelper;
use \App\Helpers\ModelImageHelper;
use \App\Helpers\PermissionsHelper;

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

	}
}