<?php

namespace App\Helpers;

use \App\Options;
use \Auth;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Facades\Cache;

class WeatherHelper
{
    protected $yahoo_settings;
    protected $accuweather_settings;
    protected $openweathermap_settings;
    protected $address;
    protected $yahoo_temperature;
    protected $yahoo_conditions = 'wi-na';
    protected $accuweather_temperature;
    protected $accuweather_conditions = 'wi-na';
    protected $openweathermap_temperature;
    protected $openweathermap_conditions = 'wi-na';

    public function __construct($city = 'Nordfjord', $country = 'NO')
    {
        $this->address = implode(', ', [$city, $country]);
        $settings = \App\Options::get('weather');
        if (!is_object($settings)) {
            $settings = new \stdClass();
        }
        if (!property_exists($settings, 'yahoo')) {
            $settings->yahoo = [
                'id' => null,
                'key' => null,
                'secret' => null,
                'enabled' => false,
            ];
        }
        if (!property_exists($settings, 'accuweather')) {
            $settings->accuweather = [
                'key' => null,
                'enabled' => false,
            ];
        }
        if (!property_exists($settings, 'openweathermap')) {
            $settings->openweathermap = [
                'key' => null,
                'enabled' => false,
            ];
        }
        $this->yahoo_settings = $settings->yahoo;
        $this->accuweather_settings = $settings->accuweather;
        $this->openweathermap_settings = $settings->openweathermap;
        $this->populateYahooWeather();
        $this->populateAccuWeather();
        $this->populateOpenWeatherMapWeather();
    }

    public function getConsensus()
    {
        $return = new \stdClass();
        $return->temp = null;
        $return->condition = 'wi-na';
        $temps = [];
        if (!is_null($this->yahoo_temperature)) {
            array_push($temps, $this->yahoo_temperature);
        }
        if (!is_null($this->accuweather_temperature)) {
            array_push($temps, $this->accuweather_temperature);
        }
        if (!is_null($this->openweathermap_temperature)) {
            array_push($temps, $this->openweathermap_temperature);
        }
        if (count($temps) > 0) {
            $return->temp = round(array_sum($temps) / count($temps), 2);
        }
        switch (true) {
            case 'wi-na' !== $this->accuweather_conditions:
                $return->condition = $this->accuweather_conditions;
                break;

            case 'wi-na' !== $this->yahoo_conditions:
                $return->condition = $this->yahoo_conditions;
                break;

            case 'wi-na' !== $this->openweathermap_conditions:
                $return->condition = $this->openweathermap_conditions;
                break;
        }
        return $return;
    }

    protected function populateYahooWeather()
    {
        if (true == $this->yahoo_settings['enabled']) {
            $this->yahoo_conditions = self::translateYahooCode(3200);
            $client = new Guzzle();
            $base_url = 'https://query.yahooapis.com/v1/public/yql';
            $woeid_yql = sprintf('select woeid from geo.places(1) where text="%s"', strtolower($this->address));
            $woeid_query = [
                'q' => $woeid_yql,
                'format' => 'json',
                'env' => 'store://datatables.org/alltableswithkeys',
            ];
            $woeid_url = sprintf('%s?%s', $base_url, http_build_query($woeid_query));
            $response = self::getCachedResults($woeid_url);
            $woeid = false;
            if (is_object($response)
                && property_exists($response, 'query')
                && is_object($response->query)
                && property_exists($response->query, 'results')
                && is_object($response->query->results)
                && property_exists($response->query->results, 'place')
                && is_object($response->query->results->place)
                && property_exists($response->query->results->place, 'woeid')
            ) {
                $woeid = intval($response->query->results->place->woeid);
            }
            if (intval($woeid) > 0) {
                $yql = sprintf('select item.condition from weather.forecast where woeid = %d and u = "%s"', $woeid, ('fahrenheit' == Auth::user()->temperature_unit) ? 'f' : 'c');
                $query = [
                    'q' => $yql,
                    'format' => 'json',
                    'env' => 'store://datatables.org/alltableswithkeys',
                ];
                $url = sprintf('%s?%s', $base_url, http_build_query($query));
                $response = self::getCachedResults($url);
                if (is_object($response)
                    && property_exists($response, 'query')
                    && is_object($response->query)
                    && property_exists($response->query, 'results')
                    && is_object($response->query->results)
                    && property_exists($response->query->results, 'channel')
                    && is_object($response->query->results->channel)
                    && property_exists($response->query->results->channel, 'item')
                    && is_object($response->query->results->channel->item)
                    && property_exists($response->query->results->channel->item, 'condition')
                    && is_object($response->query->results->channel->item->condition)
                    && property_exists($response->query->results->channel->item->condition, 'code')
                    && property_exists($response->query->results->channel->item->condition, 'temp')
                ) {
                    $this->yahoo_temperature = intval($response->query->results->channel->item->condition->temp);
                    $this->yahoo_conditions = self::translateYahooCode($response->query->results->channel->item->condition->code);
                }
            }
        }
    }

    protected function populateAccuWeather()
    {
        if (true == $this->accuweather_settings['enabled']) {
            $client = new Guzzle();
            // Get Location Key
            $location_query = [
                'apikey' => $this->accuweather_settings['key'],
                'q' => $this->address,
                'language' => 'en-us',
            ];
            $location_url = sprintf('http://dataservice.accuweather.com/locations/v1/search?%s', http_build_query($location_query));
            $response = self::getCachedResults($location_url);
            if (is_array($response) && count($response) > 0) {
                $first = array_shift($response);
                if (is_object($first) && property_exists($first, 'Key')) {
                    $key = $first->Key;
                    $conditions_query = [
                        'apikey' => $this->accuweather_settings['key'],
                        'language' => 'en-us',
                    ];
                    $conditions_url = sprintf('http://dataservice.accuweather.com/currentconditions/v1/%s?%s', $key, http_build_query($conditions_query));
                    $response = self::getCachedResults($conditions_url);
                    if (is_array($response) && count($response) > 0) {
                        $first = array_shift($response);
                        if (is_object($first)) {
                            if (property_exists($first, 'Temperature')) {
                                $temperature_key = ('fahrenheit' == Auth::user()->temperature_unit) ? 'Imperial' : 'Metric';
                                $this->accuweather_temperature = intval($first->Temperature->{$temperature_key}->Value);
                            }
                            $this->accuweather_conditions = self::translateAccuweatherCode($first->WeatherIcon);
                        }
                    }
                }
            }
        }
    }

    protected function populateOpenWeatherMapWeather()
    {
        if (true == $this->openweathermap_settings['enabled']) {
            $client = new Guzzle();
            $query = [
                'q' => $this->address,
                'units' => ('fahrenheit' == Auth::user()->temperature_unit) ? 'imperial' : 'metric',
                'APPID' => $this->openweathermap_settings['key'],
            ];
            $url = sprintf('https://api.openweathermap.org/data/2.5/weather?%s', http_build_query($query));
            $response = self::getCachedResults($url);
            if (is_object($response)) {
                if (property_exists($response, 'main')
                    && is_object($response->main)
                    && property_exists($response->main, 'temp')
                ) {
                    $this->openweathermap_temperature = intval($response->main->temp);
                }
                if (property_exists($response, 'weather') && is_array($response->weather)) {
                    $w = array_shift($response->weather);
                    if (is_object($w)
                        && property_exists($w, 'icon')
                    ) {
                        $this->openweathermap_conditions = self::translateOpenWeatherMapIcon($w->icon);
                    }
                }
            }
        }
    }

    protected static function translateYahooCode($code)
    {
        $options = [
            '0' => 'wi-tornado',
            '1' => 'wi-hurricane',
            '2' => 'wi-hurricane',
            '3' => 'wi-thunderstorm',
            '4' => 'wi-storm-showers',
            '5' => 'wi-rain-mix',
            '6' => 'wi-sleet',
            '7' => 'wi-snow-wind',
            '8' => 'wi-hail',
            '9' => 'wi-sprinkle',
            '10' => 'wi-rain',
            '11' => 'wi-rain',
            '12' => 'wi-rain',
            '13' => 'wi-snowflake-cold',
            '14' => 'wi-snowflake-cold',
            '15' => 'wi-snow-wind',
            '16' => 'wi-snowflake-cold',
            '17' => 'wi-hail',
            '18' => 'wi-sleet',
            '19' => 'wi-dust',
            '20' => 'wi-fog',
            '21' => 'wi-smog',
            '22' => 'wi-smoke',
            '23' => 'wi-windy',
            '24' => 'wi-strong-wind',
            '25' => 'wi-snowflake-cold',
            '26' => 'wi-cloudy',
            '27' => 'wi-night-alt-cloudy',
            '28' => 'wi-wi-day-cloudy',
            '29' => 'wi-night-alt-cloudy',
            '30' => 'wi-day-cloudy',
            '31' => 'wi-night-clear',
            '32' => 'wi-day-sunny',
            '33' => 'wi-stars',
            '34' => 'wi-day-sunny',
            '35' => 'w-irain-mix',
            '36' => 'wi-hot',
            '37' => 'wi-thunderstorm',
            '38' => 'wi-thunderstorm',
            '39' => 'wi-thunderstorm',
            '40' => 'wi-sprinkle',
            '41' => 'wi-snow-wind',
            '42' => 'wi-snow-wind',
            '43' => 'wi-snow-wind',
            '44' => 'wi-cloudy',
            '45' => 'wi-storm-showers',
            '46' => 'wi-snow-wind',
            '47' => 'wi-storm-showers',
            '3200' => 'wi-na',
        ];
        if (array_key_exists($code, $options)) {
            return $options[$code];
        }
        return 'wi-na';
    }

    protected static function translateAccuweatherCode($code)
    {
        $options = [
            '1' => 'wi-day-sunny',
            '2' => 'wi-day-cloudy-high',
            '3' => 'wi-day-cloudy',
            '4' => 'wi-cloudy',
            '5' => 'wi-day-haze',
            '6' => 'wi-cloudy',
            '7' => 'wi-cloudy',
            '8' => 'wi-cloudy',
            '11' => 'wi-fog',
            '12' => 'wi-raindrops',
            '13' => 'wi-showers',
            '14' => 'wi-day-showers',
            '15' => 'wi-thunderstorm',
            '16' => 'wi-thunderstorm',
            '17' => 'wi-day-thunderstorm',
            '18' => 'wi-rain',
            '19' => 'wi-snow',
            '20' => 'wi-snow',
            '21' => 'wi-day-snow',
            '22' => 'wi-snow',
            '23' => 'wi-snow',
            '24' => 'wi-snowflake-cold',
            '25' => 'wi-snowflake-cold',
            '26' => 'wi-snow-wind',
            '29' => 'wi-rain-mix',
            '30' => 'wi-hot',
            '31' => 'wi-snowflake-cold',
            '32' => 'wi-windy',
            '33' => 'wi-night-clear',
            '34' => 'wi-night-clear',
            '35' => 'wi-cloudy',
            '36' => 'wi-cloudy',
            '37' => 'wi-night-fog',
            '38' => 'wi-cloudy',
            '39' => 'wi-showers',
            '40' => 'wi-showers',
            '41' => 'wi-thunderstorm',
            '42' => 'wi-thunderstorm',
            '43' => 'wi-snow',
            '44' => 'wi-snow',
        ];
        if (array_key_exists($code, $options)) {
            return $options[$code];
        }
        return 'wi-na';
    }

    protected static function translateOpenWeatherMapIcon($icon)
    {
        $options = [
            '01d' => 'wi-day-sunny',
            '01n' => 'wi-night-clear',
            '02d' => 'wi-day-cloudy',
            '02n' => 'wi-night-alt-cloudy',
            '03d' => 'wi-cloudy',
            '03n' => 'wi-cloudy',
            '04d' => 'wi-cloud',
            '04n' => 'wi-cloud',
            '09d' => 'wi-rain',
            '09n' => 'wi-rain',
            '10d' => 'wi-day-rain',
            '10n' => 'wi-night-alt-rain',
            '11d' => 'wi-wi-thunderstorm',
            '11n' => 'wi-wi-thunderstorm',
            '13d' => 'wi-wi-snow',
            '13n' => 'wi-wi-snow',
            '50d' => 'wi-wi-fog',
            '50n' => 'wi-wi-fog',
        ];
        if (array_key_exists($icon, $options)) {
            return $options[$icon];
        }
        return 'wi-na';
    }

    protected static function getCachedResults($url, $cacheTimeoutMinutes = 60)
    {
        $cacheKey = md5($url);
        $response = Cache::get($cacheKey, false);
        if (false == $response) {
            $client = new Guzzle();
            try {
                $r = $client->request('GET', $url);
                $response = json_decode($r->getBody());
                if (false === $cacheTimeoutMinutes) {
                    Cache::forever($cacheKey, $response);
                } else {
                    $expiresAt = now()->addMinutes($cacheTimeoutMinutes);
                    Cache::put($cacheKey, $response, $expiresAt);
                }
            } catch (\Exception $e) {
                $response = false;
            }
        }
        return $response;
    }
}
