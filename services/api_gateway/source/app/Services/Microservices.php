<?php

declare(strict_types=1);

namespace App\Services;

class Microservices
{
    public const USERS_MICROSERVICE_NAME = 'users';

    public static function getAll(): array
    {
        return config('microservices');
    }

    public static function getAllWithUrl(): array
    {
        $result = [];

        foreach (self::getAll() as $service => $params) {
            if (!isset($params['url'])) {
                continue;
            }

            $result[$service] = $params['url'];
        }

        return $result;
    }

    public static function getUsersMicroserviceUrl(): string
    {
        foreach (self::getAllWithUrl() as $service => $url) {
            if ($service === self::USERS_MICROSERVICE_NAME) {
                return $url;
            }
        }

        throw new \Exception('Users microservice not found');
    }

    public static function getAllPublicRoutes(): array
    {
        $result = [];

        foreach (self::getAll() as $service => $params) {
            $publicRoutes = $params['public_routes'] ?? [];

            foreach ($publicRoutes as $publicRoute) {
                $result[] = 'api/' . $service . '/' . $publicRoute;
            }
        }

        return $result;
    }
}
