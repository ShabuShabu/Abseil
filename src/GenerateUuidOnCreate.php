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
            if (! config('abseil.use_uuids')) {
                return;
            }

            if (is_string($id = $model->getKey()) && Uuid::isValid($id)) {
                return;
            }

            $model->setAttribute(
                $model->getKeyName(),
                Str::orderedUuid()->toString()
            );
        });
    }
}
