<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Microservices;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    public function forward(Request $request, $service): Application|Response|JsonResponse|ResponseFactory
    {
        // Карта сервисов
        $services = Microservices::getAllWithUrl();

        // Проверяем, существует ли запрашиваемый сервис
        if (!array_key_exists($service, $services)) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        // Генерируем URL для запроса
        $url = $services[$service] . '/' . $request->path();

        $url = str_replace("/api/$service/", '/api/', $url); // Удаляем '{service}' из пути

        // Прокси запрос
        $response = Http::withHeaders($request->headers->all())
            ->send($request->method(), $url, [
                'query' => $request->query(),
                'json'  => $request->all(),
            ]);

        // Возвращаем ответ
        return response($response->body(), $response->status());
    }
}
