<?php

namespace ShabuShabu\Abseil\Events;

use Illuminate\Database\Eloquent\Model;

class ResourceRelationshipSaved extends BaseEvent
{
    /**
     * @var Model|\Illuminate\Database\Eloquent\Collection
     */
    public $relationship;

    public string $type;

    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $type
     * @param mixed                               $relationship
     */
    public function __construct(Model $model, string $type, $relationship)
    {
        parent::__construct($model);

        $this->relationship = $relationship;
        $this->type         = $type;
    }
}
