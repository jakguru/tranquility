<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helpers\ElasticSearchClientHelper;

class ElasticSearchHelperTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetClient()
    {
        $client = ElasticSearchClientHelper::getClient();
        if (config('app.es.enabled')) {
            $this->assertInstanceOf('Elasticsearch\Client', $client, 'Helper did not return an ElasticSearch Client.');
        } else {
            $this->assertFalse($client, 'Helper did not return "false".');
        }
    }

    public function testClientCanConnect()
    {
        $client = ElasticSearchClientHelper::getClient();
        if (config('app.es.enabled')) {
            $this->assertTrue(ElasticSearchClientHelper::clientCanConnect($client), 'Cannot connect to ElasticSearch Database');
        } else {
            $this->assertFalse($client, 'Helper did not return "false".');
        }
    }
}
