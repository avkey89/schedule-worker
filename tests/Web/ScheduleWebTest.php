<?php

declare(strict_types=1);

namespace App\Tests\Web;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScheduleWebTest extends WebTestCase
{
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
    }

    protected function tearDown(): void
    {
        $this->client = null;
        parent::tearDown();
    }

    public function testScheduleSuccess()
    {
        $this->client->request("GET", "/schedule-worker", ["userId"=>1, "startDate"=>"2020-12-01", "endDate"=>"2021-01-31"]);
        $response = $this->client->getResponse()->getContent();
        $json = json_decode($response, true);

        $this->assertTrue($json["success"]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testScheduleErrorEmptyStartDate()
    {
        $this->client->request("GET", "/schedule-worker", ["userId"=>1, "endDate"=>"2021-01-31"]);
        $response = $this->client->getResponse()->getContent();
        $json = json_decode($response, true);

        $this->assertFalse($json["success"]);
        $this->assertEquals("Enter date start", $json["error"][0]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testScheduleExceptionNotFoundWorker()
    {
        $this->client->request("GET", "/schedule-worker", ["userId"=>5, "startDate"=>"2020-12-01", "endDate"=>"2021-01-31"]);
        $response = $this->client->getResponse()->getContent();
        $json = json_decode($response, true);

        $this->assertFalse($json["success"]);
        $this->assertEquals("Worker not found", $json["error"]);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}