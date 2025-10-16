<?php

namespace App\Service;

use App\Interface\WeatherApiClientInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * WeatherService
 * Fetchs from cache first if available, otherwise fetchs from weather data API Clien (injected via DI) and cache the result for 5 minutes
 * Uses CacheInterface to cache the result (via cache adapter in services.yaml).
 *
 * @return Returns weather data as array or throw exception if API error
 */
class WeatherService
{
    /**
     * 5 minutes cache time.
     */
    private const CACHE_TTL = 300;

    public function __construct(
        private WeatherApiClientInterface $client,
        private CacheInterface $cache,
    ) {}

    public function getWeather(array $params): array
    {
        $cacheKey = 'app_weather_' . md5(json_encode($params));

        // CacheInterface::get atomic cachekey lock to prevent race condition from multi request trying to set cache
        return $this->cache->get($cacheKey, function ($item) use ($params) {
            try {
                // get forecast api result
                $apiResponse = $this->client->getForecast($params);
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
