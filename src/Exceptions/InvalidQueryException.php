<?php

namespace ShabuShabu\Abseil\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use ShabuShabu\Abseil\ApiError;
use ShabuShabu\Abseil\Http\Resources\Resource;
use Spatie\QueryBuilder\Exceptions\InvalidFieldQuery;
use Spatie\QueryBuilder\Exceptions\InvalidQuery;

/**
 * @see https://jsonapi.org/examples/#error-objects-invalid-query-parameters
 */
class InvalidQueryException extends Exception implements Responsable
{
    /**
     * @param \Spatie\QueryBuilder\Exceptions\InvalidQuery $previous
     */
    protected function __construct(InvalidQuery $previous)
    {
        parent::__construct(
            'Invalid Query Parameters',
            Response::HTTP_BAD_REQUEST,
            $previous
        );
    }

    /**
     * @param \Spatie\QueryBuilder\Exceptions\InvalidQuery $exception
     * @return InvalidQueryException
     */
    public static function from(InvalidQuery $exception): InvalidQueryException
    {
        return new static($exception);
    }

    /**
     * @return string
     */
    protected function parameter(): string
    {
        if ($this->getPrevious() instanceof InvalidFieldQuery) {
            $key = 'fields';
        } else {
            $className = strtolower(class_basename($this->getPrevious()));
            $key       = str_replace(['invalid', 'query'], '', $className);
        }

        return config('query-builder.parameters.' . $key, '');
    }

    /**
     * @inheritDoc
     */
    public function toResponse($request)
    {
        return response()->json([
            'errors' => [
                ApiError::make(
                    $this->getCode(),
                    $this->getPrevious()->getMessage(),
                    $this->getMessage()
                )->parameter(
                    $this->parameter()
                ),
            ],
        ], $this->getCode(), Resource::DEFAULT_HEADERS);
    }
}
