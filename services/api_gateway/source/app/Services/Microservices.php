<?php

declare(strict_types=1);

namespace App\Services;

class Microservices
{
    public const USERS_MICROSERVICE_NAME = 'users';

    public function getAll(): array
    {
        return config('microservices');
    }

    public function getAllWithUrl(): array
    {
        $result = [];

        foreach ($this->getAll() as $service => $params) {
            if (!isset($params['url'])) {
                continue;
            }

            $result[$service] = $params['url'];
        }

        return $result;
    }

    public function getUrlByMicroservice(string $service): string
    {
        return $this->getAll()[$service]['url'];
    }

    public function getAllPublicRoutes(): array
    {
        $result = [];

        foreach ($this->getAll() as $service => $params) {
            $publicRoutes = $params['public_routes'] ?? [];

            foreach ($publicRoutes as $publicRoute) {
                $result[] = 'api/' . $service . '/' . $publicRoute;
            }
        }

        return $result;
    }
}
