<?php

namespace App\Helpers;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Client;
use Illuminate\Support\Facades\Log;

class ElasticSearchClientHelper
{
    public static function getClient()
    {
        if (!config('app.es.enabled')) {
            return false;
        }
        $host = [
            'host' => config('app.es.host'),
            'port' => config('app.es.port'),
            'scheme' => config('app.es.scheme'),
            'user' => config('app.es.user'),
            'pass' => config('app.es.pass'),
        ];
        if (is_null($host['user']) || is_null($host['pass'])) {
            unset($host['user']);
            unset($host['pass']);
        }
        if (true == config('app.debug')) {
            $logger = ClientBuilder::defaultLogger('storage/logs/elasticsearch.log');
            $client = ClientBuilder::create()
              ->setHosts([$host])
              ->setRetries(0)
              ->setLogger($logger)
              ->build();
        } else {
            $client = ClientBuilder::create()
            ->setHosts([$host])
            ->setRetries(0)
            ->build();
        }
        return $client;
    }

    public static function clientCanConnect(Client $client)
    {
        $return = false;
        try {
            $response = $client->cluster()->stats();
            $return = true;
        } catch (\Exception $e) {
            $response = json_decode($e->getMessage());
        }
        return $return;
    }
}
