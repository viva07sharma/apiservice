<?php

namespace App\Client;

use App\Interface\WeatherApiClientInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Client for OpenMeteo weather API
 */
class OpenMeteoClient implements WeatherApiClientInterface
{
    /**
     * Base URL for the OpenMeteo API
     */
    private const API_BASE_URL = 'https://api.open-meteo.com/v1';

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * Fetch the weather forecast data from OpenMeteo API for given parameters
     *
     * @param array $params Parameters to fetch weather data for
     *                      - 'latitude' (float)   The latitude coordinate to fetch weather data for
     *                      - 'longitude' (float)  The longitude coordinate to fetch weather data for
     *                      - 'currentTemperature' (string)  Whether to fetch current temperature data
     *                      - 'hourlyTemperature' (string)   Whether to fetch hourly temperature data
     *                      - 'forecastDays' (int)          Number of days to fetch forecast data for
     *
     * @return array Weather forecast data as array or error response if request fails
     */
    public function getForecast(array $params): array
    {
        $url = self::API_BASE_URL.'/forecast';
        $response = $this->httpClient->request('GET', $url, [
            'query' => [
                'latitude' => $params['latitude'],
                'longitude' => $params['longitude'],
                'current' => $params['currentTemperature'],
                'hourly' => $params['hourlyTemperature'],
                'forecast_days' => $params['forecastDays'],
            ],
        ]);

        return $response->toArray();
    }
}
