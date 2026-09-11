<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser()) {
            $user = Auth::user();
            // If user has a specific branch assigned and is not a superadmin (role_id !== 1)
            if (!empty($user->branch_id) && $user->role_id !== 1) {
                $builder->where($model->getTable() . '.branch_id', $user->branch_id);
            }
        }
    }
}
