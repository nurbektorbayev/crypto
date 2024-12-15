<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR); // Вы можете менять код ошибки на более подходящий
        }

        return parent::render($request, $e);
    }
}
