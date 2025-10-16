<?php

namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\WeatherService;

class ApiControllerTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    public function testApiReturnsWeatherDataWhenServiceAvailable(): void
    {
        $client = static::createClient();

        $mockResponse = ['temp' => 20];

        // Replace WeatherService in the container with a mock
        $mockService = $this->createMock(WeatherService::class);
        $mockService->expects($this->once())
            ->method('getWeather')
            ->willReturn($mockResponse);

        $container = static::getContainer();
        // set the mock into the container so the controller receives it
        $container->set(WeatherService::class, $mockService);

        $client->request('GET', '/api?latitude=1.1&longitude=2.2&current=true&hourly=temperature_2m&forecast_days=1');

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(json_encode($mockResponse), $client->getResponse()->getContent());
    }

    public function testApiReturns503WhenServiceThrows(): void
    {
        $client = static::createClient();

        $mockService = $this->createMock(WeatherService::class);
        $mockService->expects($this->once())
            ->method('getWeather')
            ->willThrowException(new \RuntimeException('API failure'));

        $container = static::getContainer();
        $container->set(WeatherService::class, $mockService);

        $client->request('GET', '/api?latitude=1.1&longitude=2.2&current=true&hourly=temperature_2m&forecast_days=1');

        $this->assertResponseStatusCodeSame(503);
        $this->assertStringContainsString('Weather Service Unavailable', $client->getResponse()->getContent());
    }
}
