<?php

namespace ShabuShabu\Abseil\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use ShabuShabu\Abseil\Http\Resources\Resource;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class JsonApiMediaType
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('content-type') !== Resource::MEDIA_TYPE) {
            throw new UnsupportedMediaTypeHttpException('An invalid content type header was set');
        }

        return $next($request);
    }
}
