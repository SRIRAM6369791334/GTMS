<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';
    protected $guarded = [];

    /**
     * Users assigned to this branch
     */
    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    /**
     * Lease applications filed under this branch
     */
    public function leaseApplications()
    {
        return $this->hasMany(LeaseApplication::class, 'branch_id');
    }

    /**
     * Mining applications filed under this branch
     */
    public function miningApplications()
    {
        return $this->hasMany(MiningApplication::class, 'branch_id');
    }
}
