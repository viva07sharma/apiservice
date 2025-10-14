<?php

namespace App\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Client for OpenMeteo weather API
 */
class OpenMeteoClient
{
    /**
     * Base URL for the OpenMeteo API
     */
    private const API_BASE_URL = 'https://api.open-meteo.com/v1';

    public function __construct(private HttpClientInterface $httpClient) {}

    /**
     * Fetch the weather forecast data from OpenMeteo API for given parameters
     *
     * @param float  $latitude          The latitude coordinate to fetch weather data for
     * @param float  $longitude         The longitude coordinate to fetch weather data for
     * @param string $currenTemperature Current temperature parameter to fetch
     * @param string $hourlyTemperature Hourly temperature parameter to fetch
     * @param int    $forecastDays      Number of days to forecast
     *
     * @return array Weather forecast data as array or error response if request fails
     */
    public function getForecast(float $latitude, float $longitude, string $currenTemperature, string $hourlyTemperature, int $forecastDays): array
    {
        $url = self::API_BASE_URL . '/forecast';
        $response = $this->httpClient->request('GET', $url, [
            'query' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => $currenTemperature,
                'hourly' => $hourlyTemperature,
                'forecast_days' => $forecastDays,
            ],
        ]);

        return $response->toArray();
    }
}
