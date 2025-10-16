<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EndToEndApiTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    public function testCompleteApiFlow(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api?latitude=52.52&longitude=13.41&current=temperature_2m&hourly=temperature_2m&forecast_days=1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        
        $response = json_decode($client->getResponse()->getContent(), true);
        
        // Test complete flow + API contract
        $this->assertIsArray($response);
        $this->assertArrayHasKey('current', $response);
        $this->assertArrayHasKey('hourly', $response);
        $this->assertArrayHasKey('current_units', $response);
        $this->assertArrayHasKey('hourly_units', $response);
    }

}