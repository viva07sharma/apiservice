<?php

namespace App\Service;

use App\Client\OpenMeteoClient;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * WeatherService
 * Fetchs from cache first if available, otherwise fetchs from weather data API and cache the result for 5 minutes
 * Uses CacheInterface to cache the result (via cache adapter in services.yaml)
 * @return Returns weather data as array or throw error if API error
 */
class WeatherService
{
    /**
     * 5 minuts cache time
     */
    private const CACHE_TTL = 300;

    public function __construct(
        private OpenMeteoClient $client,
        private CacheInterface $cache
    ) {}

    public function getWeather(
        float $latitude,
        float $longitude,
        string $currenTemperature,
        string $hourlyTemperature,
        int $forecastDays
    ): array {
        $cacheKey = sprintf('app:weather_%s_%s_%s_%s_%s', $latitude, $longitude, $currenTemperature, $hourlyTemperature, $forecastDays);

        // CacheInterface::get atomic cachekey lock to prevent race condition from multi request trying to set cache
        return $this->cache->get($cacheKey, function ($item) use ($latitude, $longitude, $currenTemperature, $hourlyTemperature, $forecastDays) {
            try {
                //get forecast api result
                $apiResponse = $this->client->getForecast($latitude, $longitude, $currenTemperature, $hourlyTemperature, $forecastDays);
            } catch (\Exception $e) {
                // API error
                throw $e;
            }

            // Set cache expiry if no error above
            $item->expiresAfter(self::CACHE_TTL);

            return $apiResponse;
        });
    }
}
