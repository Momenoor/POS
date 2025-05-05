<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class UserIdObserver
{
    public function creating(Model $model): void
    {
        if (auth()->check() && $model->isFillable('user_id')) {
            $model->user_id = auth()->id();
        }
    }
}
