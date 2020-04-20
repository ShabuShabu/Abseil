<?php

namespace ShabuShabu\Abseil\Exceptions;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as BaseException;
use ShabuShabu\Abseil\Error;
use ShabuShabu\Abseil\Http\Resources\Resource;
use function ShabuShabu\Abseil\to_camel_case;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends BaseException implements Responsable
{
    /**
     * {@inheritDoc}
     */
    public static function withMessages(array $messages): BaseException
    {
        return parent::withMessages(to_camel_case($messages));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function jsonApiErrors(Request $request): array
    {
        $errors = collect();

        foreach ($this->errors() as $key => $messages) {
            foreach ($messages as $message) {
                $errors = $errors->push(
                    Error::make(Response::HTTP_UNPROCESSABLE_ENTITY, $message)->pointer($request, $key)
                );
            }
        }

        return [
            'errors' => $errors->toArray(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function toResponse($request)
    {
        return response()->json(
            $this->jsonApiErrors($request),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Resource::DEFAULT_HEADERS
        );
    }
}
