<?php

namespace ShabuShabu\Abseil\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class CheckMediaType
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string                   $mediaType
     * @param string                   $header
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $mediaType, string $header = 'content-type')
    {
        if ($request->header($header) !== $mediaType) {
            throw new UnsupportedMediaTypeHttpException(sprintf('An invalid %s header was set', strtolower($header)));
        }

        return $next($request);
    }
}
