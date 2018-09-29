<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use \Route;
use Illuminate\Database\Eloquent\Model;
use \App\Helpers\CountryHelper;

class ModelImageHelper
{
    protected $api_key;
    protected $default_address;
    protected $maps_enabled;

    public function __construct()
    {
        $settings = \App\Options::get('google');
        if (!is_object($settings)) {
            $settings = new \stdClass();
        }
        if (!property_exists($settings, 'maps')) {
            $settings->maps = [
                'key' => null,
                'address' => 'Nordfjord, Norway',
                'enabled' => false,
            ];
        }
        $this->api_key = $settings->maps['key'];
        $this->default_address = $settings->maps['address'];
        $this->maps_enabled = $settings->maps['enabled'];
    }

    public function getBackgroundImage(Request $request, $model, $model_id)
    {
        header('Content-Type: image/png');
        if (is_subclass_of($model, 'Illuminate\Database\Eloquent\Model', true)) {
            $obj = $model::find($model_id);
            if (is_subclass_of($obj, 'Illuminate\Database\Eloquent\Model', true)) {
                if ($this->maps_enabled) {
                    $address_array = [];
                    if (!is_null($obj->address_line_1) && strlen($obj->address_line_1) > 0) {
                        array_push($address_array, $obj->address_line_1);
                    }
                    if (!is_null($obj->address_line_2) && strlen($obj->address_line_2) > 0) {
                        array_push($address_array, $obj->address_line_2);
                    }
                    $address_line_3_array = [];
                    if (!is_null($obj->city) && strlen($obj->city) > 0) {
                        array_push($address_line_3_array, $obj->city);
                    }
                    if (!is_null($obj->state) && strlen($obj->state) > 0) {
                        array_push($address_line_3_array, $obj->state);
                    }
                    if (!is_null($obj->postal) && strlen($obj->postal) > 0) {
                        array_push($address_line_3_array, $obj->postal);
                    }
                    if (count($address_line_3_array) > 0) {
                        array_push($address_array, implode(', ', $address_line_3_array));
                    }
                    if (!is_null($obj->country) && strlen($obj->country) > 0 && 'XX' !== $obj->country) {
                        array_push($address_array, CountryHelper::getCountryName($obj->country));
                    }
                    if (count($address_array) > 0) {
                        $address = implode("\n", $address_array);
                    } else {
                        $address = $this->default_address;
                    }
                    $map_file_name = sprintf('%s.map.png', md5($address));
                    if (Storage::exists($map_file_name)) {
                        $contents = Storage::get($map_file_name);
                    } else {
                        $base_url = 'https://maps.googleapis.com/maps/api/staticmap';
                        $query = [
                            'center' => trim(preg_replace("/\r|\n/", "", $address)),
                            'size' => '640x250',
                            'maptype' => 'roadmap',
                            'key' => $this->api_key,
                            'format' => 'png',
                            'markers' => sprintf('color:red|%s', trim(preg_replace("/\r|\n/", "", $address))),
                        ];
                        $full_url = sprintf('%s?%s', $base_url, http_build_query($query));
                        $contents = file_get_contents($full_url);
                        Storage::put($map_file_name, $contents, 'private');
                    }
                    $img = @imagecreatefromstring($contents);
                } else {
                    $path = public_path('img/backgrounds/');
                    $images = [];
                    try {
                        $df = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
                        foreach ($df as $item) {
                            if ($item->isReadable() && $item->isFile()) {
                                $image_info = [];
                                $image_file = $item->getRealPath();
                                $size = @getimagesize($image_file);
                                if (false !== $size) {
                                    array_push($images, $image_file);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // do nothing
                    }
                    if (count($images) > 0) {
                        $img_path = $images[array_rand($images)];
                        $img = @imagecreatefromstring(File::get($img_path));
                    }
                    if (!isset($img) || !$img) {
                        $img = imagecreatetruecolor(640, 250);
                        $bg = imagecolorallocate($img, 255, 255, 255);
                        imagefilledrectangle($img, 0, 0, 640, 250, $bg);
                    }
                    $layer = imagecreatetruecolor(640, 250);
                    $bg = imagecolorallocatealpha($layer, 255, 255, 255, 0);
                    imagefilledrectangle($layer, 0, 0, 640, 250, $bg);
                    imagecopymerge($img, $layer, 0, 0, 0, 0, 640, 250, 50);
                }
                if (isset($img) && false !== $img) {
                    imagepng($img);
                    imagedestroy($img);
                }
            }
        }
        exit();
    }

    public function getAvatarImage(Request $request, $model, $model_id)
    {
        header('Content-Type: image/png');
        $size = $request->input('s', 200);
        $default = $request->input('d', 'mp');
        $rating = $request->input('r', 'r');
        if (is_subclass_of($model, 'Illuminate\Database\Eloquent\Model', true)) {
            $obj = $model::find($model_id);
            if (is_subclass_of($obj, 'Illuminate\Database\Eloquent\Model', true)) {
                if (!is_null($obj->avatar) && strlen($obj->avatar) > 0) {
                    $img = @imagecreatefromstring(File::get($obj->avatar));
                } elseif (!is_null($obj->email) && strlen($obj->email) > 0) {
                    $url = sprintf('https://www.gravatar.com/avatar/%s?%s', md5($obj->email), http_build_query([
                        's' => intval($size),
                        'd' => $default,
                        'r' => $rating,
                    ]));
                    $img_name = sprintf('%s.avatar.png', md5(sprintf('%s %s %s %s', $obj->email, $size, $default, $rating)));
                    if (Storage::exists($img_name)) {
                        $contents = Storage::get($img_name);
                    } else {
                        $contents = file_get_contents($url);
                        Storage::put($img_name, $contents, 'private');
                    }
                    $img = @imagecreatefromstring($contents);
                } else {
                    $url = sprintf('https://www.gravatar.com/avatar/%s?%s', md5(config('app.name')), http_build_query([
                        's' => intval($size),
                        'd' => $default,
                        'r' => $rating,
                        'f' => 'y'
                    ]));
                    $img_name = sprintf('%s.avatar.png', md5(sprintf('%s %s %s %s', config('app.name'), $size, $default, $rating)));
                    if (Storage::exists($img_name)) {
                        $contents = Storage::get($img_name);
                    } else {
                        $contents = file_get_contents($url);
                        Storage::put($img_name, $contents, 'private');
                    }
                    $img = @imagecreatefromstring($contents);
                }
                imagepng($img);
                imagedestroy($img);
            }
        }
        exit();
    }

    public static function getUrlForBackgroundImage($model, $model_id)
    {
        if (is_object($model)) {
            $model = get_class($model);
        }
        return route('get-model-background', ['model' => $model, 'id' => $model_id]);
    }

    public static function getUrlForAvatarImage($model, $model_id)
    {
        if (is_object($model)) {
            $model = get_class($model);
        }
        return route('get-model-avatar', ['model' => $model, 'id' => $model_id]);
    }
}
