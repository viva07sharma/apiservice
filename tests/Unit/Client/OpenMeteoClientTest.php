<?php

namespace App\Tests\Unit;

use App\Client\OpenMeteoClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class OpenMeteoClientTest extends TestCase
{
    public function testGetForecastBuildsCorrectUrlAndParsesResponse(): void
    {
        $capturedUrl = null;

        $mockResponseData = ['current_weather' => ['temperature' => 15.2]];
        $mockResponse = new MockResponse(json_encode($mockResponseData), [
            'http_code' => 200,
            'headers' => ['content-type' => 'application/json'],
        ]);

        // Create a MockHttpClient that returns MockResponse and captures the requested URL
        $client = new MockHttpClient(function ($method, $url, $options) use ($mockResponse, &$capturedUrl) {
            $capturedUrl = $url;
            return $mockResponse;
        });

        $openMeteo = new OpenMeteoClient($client);

        $params = [
            'latitude' => 12.0,
            'longitude' => 56.78,
            'currentTemperature' => 'true',
            'hourlyTemperature' => 'temperature_2m',
            'forecastDays' => 3,
        ];

        $result = $openMeteo->getForecast($params);

        $this->assertStringContainsString('/forecast', $capturedUrl);
        $this->assertStringContainsString('latitude=12', $capturedUrl);
        $this->assertStringContainsString('longitude=56.78', $capturedUrl);
        $this->assertStringContainsString('current=true', $capturedUrl);
        $this->assertStringContainsString('hourly=temperature_2m', $capturedUrl);
        $this->assertStringContainsString('forecast_days=3', $capturedUrl);
        $this->assertSame($mockResponseData, $result);
    }
}
