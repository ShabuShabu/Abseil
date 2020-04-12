<?php

namespace ShabuShabu\Abseil\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResourceDeleted
{
    use Dispatchable, SerializesModels;

    public Model $model;

    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
