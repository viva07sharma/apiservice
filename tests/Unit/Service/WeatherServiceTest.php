<?php

namespace App\Tests\Unit\Service;

use App\Service\WeatherService;
use App\Interface\WeatherApiClientInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

class WeatherServiceTest extends TestCase
{
    public function testGetWeatherReturnsApiData(): void
    {
        $params = ['latitude' => 1.0, 'longitude' => 2.0];
        $expectedResponse = ['temp' => 12.3, 'units' => 'C'];

        // Mock API client
        $client = $this->createMock(WeatherApiClientInterface::class);
        $client->method('getForecast')->willReturn($expectedResponse);

        // Mock cache
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn($expectedResponse);

        $service = new WeatherService($client, $cache);

        $result = $service->getWeather($params);

        $this->assertSame($expectedResponse, $result);
    }

    public function testGetWeatherCacheMissStoresApiData(): void
    {
        $params = ['latitude' => 3.0, 'longitude' => 4.0];
        $expectedResponse = ['temp' => 18.5, 'units' => 'C'];

        // Mock API client returns data when cache misses
        $client = $this->createMock(WeatherApiClientInterface::class);
        $client->method('getForecast')->willReturn($expectedResponse);

        // Mock cache item with expiresAfter
        $mockCacheItem = new class {
            public function expiresAfter(int $seconds) {}
        };

        // Mock cache to simulate cache miss
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturnCallback(fn($key, $callback) => $callback($mockCacheItem));

        $service = new WeatherService($client, $cache);

        $result = $service->getWeather($params);

        $this->assertSame($expectedResponse, $result);
    }


    public function testGetWeatherPropagatesApiException(): void
    {
        $params = ['latitude' => 5.0, 'longitude' => 6.0];

        // Mock API client
        $client = $this->createMock(WeatherApiClientInterface::class);
        $client->method('getForecast')->willThrowException(new \RuntimeException('API error'));

        // Mock cache item with expiresAfter
        $mockCacheItem = new class {
            public function expiresAfter(int $seconds) {}
        };

        // Mock cache miss and api fetch results in error
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturnCallback(fn($key, $callback) => $callback($mockCacheItem));

        $service = new WeatherService($client, $cache);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('API error');

        $service->getWeather($params);
    }
}
