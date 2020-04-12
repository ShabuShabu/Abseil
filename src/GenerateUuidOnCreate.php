<?php

namespace ShabuShabu\Abseil;

use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

trait GenerateUuidOnCreate
{
    /**
     * @return void
     */
    protected static function bootGenerateUuidOnCreate(): void
    {
        static::creating(static function (Model $model) {
            if (!Uuid::isValid($model->getKey())) {
                $model->setAttribute($this->getKeyName(), Str::orderedUuid()->toString());
            }
        });
    }
}
