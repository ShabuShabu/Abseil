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
     * @param string                   $headerName
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $headerName = 'content-type')
    {
        $header    = $request->header($headerName);
        $mediaType = Resource::MEDIA_TYPE;

        if ($header !== $mediaType) {
            throw new UnsupportedMediaTypeHttpException(
                "An invalid $headerName header was set [$header]. Expected [$mediaType]."
            );
        }

        return $next($request);
    }
}
