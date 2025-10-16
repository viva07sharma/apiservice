<?php

namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller to call the weather service based on query parameters.
 *
 * @param Request        $request        The request object
 * @param WeatherService $weatherService The weather service
 *
 * @return JsonResponse Returns a JSON response with the weather data or an error message
 */
final class ApiController extends AbstractController
{
    #[Route('/api', name: 'weather_api')]
    public function index(Request $request, WeatherService $weatherService): JsonResponse
    {
        $params = [
            'latitude' => (float) $request->query->get('latitude'),
            'longitude' => (float) $request->query->get('longitude'),
            'currentTemperature' => $request->query->get('current'),
            'hourlyTemperature' => $request->query->get('hourly'),
            'forecastDays' => (int) $request->query->get('forecast_days'),
        ];
        try {
            $data = $weatherService->getWeather($params);

            return $this->json($data);
        } catch (\Exception $e) {
            // Service unavailable
            $data = [
                'error' => 'Weather Service Unavailable',
            ];

            return $this->json($data, 503);
        }
    }
}
