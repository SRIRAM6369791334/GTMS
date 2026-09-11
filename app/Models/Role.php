<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $table = 'roles';

    // Direct users assigned via users.role_id column
    public function directUsers()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}

