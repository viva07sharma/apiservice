<?php

namespace App\Interface;

/**
 * Interface for Weather API Client
 */
interface WeatherApiClientInterface
{
    /**
     * Fetch weather forecast for a given location
     * @param array $params Parameters for the API request
     * @return array Parsed API response or throw exception if API error
     */
    public function getForecast(array $params): array;
}
