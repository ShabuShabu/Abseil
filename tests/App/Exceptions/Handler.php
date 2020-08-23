<?php

namespace ShabuShabu\Abseil\Tests\App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException as BaseValidationException;
use ShabuShabu\Abseil\Exceptions\InvalidQueryException;
use ShabuShabu\Abseil\Exceptions\ValidationException;
use Spatie\QueryBuilder\Exceptions\InvalidQuery;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception): Response
    {
        if ($exception instanceof BaseValidationException) {
            $exception = ValidationException::withMessages($exception->errors());
        }

        if ($exception instanceof InvalidQuery) {
            $exception = InvalidQueryException::from($exception);
        }

        return parent::render($request, $exception);
    }
}
