<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServerControllerTest extends WebTestCase
{
    public function testListServers()
    {
        $client = static::createClient();

        $client->request('GET', '/server/list');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        // Test filtering by RAM
        $client->request('GET', '/server/list?ram=16GB,32GB');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        foreach ($data['data'] as $server) {
            $this->assertMatchesRegularExpression('/^(16|32)GB/', $server['ram']);
        }

        // Test filtering by location
        $client->request('GET', '/server/list?location=AmsterdamAMS-01');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        foreach ($data['data'] as $server) {
            $this->assertEquals('AmsterdamAMS-01', $server['location']);
        }
    }
}
