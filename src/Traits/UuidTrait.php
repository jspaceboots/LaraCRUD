<?php
namespace jspaceboots\laracrud\Traits;

use Webpatser\Uuid\Uuid as U;

trait UuidTrait
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = U::generate()->string;
        });
    }
}