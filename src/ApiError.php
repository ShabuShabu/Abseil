<?php

namespace ShabuShabu\Abseil;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class ApiError implements Arrayable
{
    protected ?Request $request;

    protected int $status;

    protected ?string $key;

    protected string $message;

    protected ?string $title;

    /**
     * @param int         $status
     * @param string      $message
     * @param string|null $title
     */
    protected function __construct(int $status, string $message, ?string $title = null)
    {
        $this->status  = $status;
        $this->message = $message;
        $this->title   = $title;
    }

    /**
     * @param int         $status
     * @param string      $message
     * @param string|null $title
     * @return ApiError
     */
    public static function make(int $status, string $message, ?string $title = null): ApiError
    {
        return new static($status, $message, $title);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string                   $key
     * @return $this
     */
    public function pointer(Request $request, string $key): self
    {
        $this->request = $request;
        $this->key     = $key;

        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function parameter(string $key): self
    {
        $this->request = null;
        $this->key     = $key;

        return $this;
    }

    /**
     * @return string|null
     */
    protected function getPointer(): ?string
    {
        return collect([
            $this->key,
            'data.' . $this->key,
            'data.attributes.' . $this->key,
            'relationships' . $this->key,
        ])->filter(
            fn(string $location) => $this->request->has($location)
        )->first();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $error = collect([
            'status' => (string)$this->status,
            'detail' => $this->message,
        ]);

        if ($this->title) {
            $error = $error->put('title', $this->title);
        }

        if ($this->key && $pointer = $this->getPointer()) {
            $error = $error->put('source', [
                'pointer' => '/' . str_replace('.', '/', $pointer),
            ]);
        }

        if (! $this->request && $this->key) {
            $error = $error->put('source', [
                'parameter' => $this->key,
            ]);
        }

        return $error->toArray();
    }
}
