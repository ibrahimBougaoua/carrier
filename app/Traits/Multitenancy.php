<?php

namespace App\Traits;
use Auth;

trait Multitenancy {

    public static function bootMultitenancy()
    {
        if(auth()->check())
        {
            static::creating(function ($model) {
                $model->user_id = Auth::user()->id;
            });

            static::addGlobalScope('user_id', function ($builder) {
                return $builder->where('user_id', Auth::user()->id);
            });
        }
    }
}
