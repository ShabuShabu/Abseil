<?php

namespace ShabuShabu\Abseil\Http;

use Illuminate\Http\Response;

trait Responding
{
    /**
     * @param string $message
     * @return \Illuminate\Http\Response
     */
    protected function notModified(string $message = 'Not Modified'): Response
    {
        return response($message, Response::HTTP_NOT_MODIFIED);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\Response
     */
    protected function badGateway(string $message = 'Bad Gateway'): Response
    {
        return response($message, Response::HTTP_BAD_GATEWAY);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\Response
     */
    protected function noContent(string $message = 'No Content'): Response
    {
        return response($message, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\Response
     */
    protected function created(string $message = 'Created'): Response
    {
        return response($message, Response::HTTP_CREATED);
    }
}
