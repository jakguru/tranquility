<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class BackgroundImageHelper
{
    protected $full_image_path;
    protected $avg_color;
    protected $avg_brightness;
    protected $body_class;
    protected $asset_path;

    public function __construct($path, $asset_path = '')
    {
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
            $this->full_image_path = $images[array_rand($images)];
            $this->avg_color = self::avgColor($this->full_image_path);
            if (is_array($this->avg_color)) {
                $cb = 0;
                $cb = $cb + $this->avg_color['r'];
                $cb = $cb + $this->avg_color['g'];
                $cb = $cb + $this->avg_color['b'];
            } else {
                $cb = 255 * 3;
            }
            $this->avg_brightness = ($cb / (255 * 3));
            $this->body_class = ($this->avg_brightness > 0.5) ? 'bright-bg' : 'dark-bg';
            $this->asset_path = sprintf('%s/%s', $asset_path, substr($this->full_image_path, strlen($path)));
        }
    }

    public static function getRandomBackgroundImage()
    {
        $c = get_called_class();
        $obj = new $c(public_path('img/backgrounds/'), 'img/backgrounds');
        Cache::forever('random-bg-body-class', $obj->body_class);
        Cache::forever('random-bg-asset-path', $obj->asset_path);
    }

    protected static function avgColor($imageFile)
    {
        $size = @getimagesize($imageFile);
        if ($size === false) {
            return false;
        }
        $img = @imagecreatefromstring(File::get($imageFile));
        if (!$img) {
            return false;
        }
        $scaled = imagescale($img, 1, 1, IMG_BICUBIC);
        $rgb = imagecolorat($img, 0, 0);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        return [
            'r' => $r,
            'g' => $g,
            'b' => $b,
        ];
    }

    protected static function colorPalette($imageFile, $numColors, $granularity = 5)
    {
        $granularity = max(1, abs((int)$granularity));
        $colors = array();
        $size = @getimagesize($imageFile);
        if ($size === false) {
            return false;
        }
        $img = @imagecreatefromstring(File::get($imageFile));
        if (!$img) {
            return false;
        }
        for ($x = 0; $x < $size[0]; $x += $granularity) {
            for ($y = 0; $y < $size[1]; $y += $granularity) {
                $thisColor = imagecolorat($img, $x, $y);
                $rgb = imagecolorsforindex($img, $thisColor);
                $red = round(round(($rgb['red'] / 0x33)) * 0x33);
                $green = round(round(($rgb['green'] / 0x33)) * 0x33);
                $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);
                $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);
                if (array_key_exists($thisRGB, $colors)) {
                    $colors[$thisRGB]++;
                } else {
                    $colors[$thisRGB] = 1;
                }
            }
        }
        arsort($colors);
        return array_slice(array_keys($colors), 0, $numColors);
    }
}
