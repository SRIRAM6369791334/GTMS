<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvironmentalDocument extends Model
{
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(EnvironmentalProject::class, 'project_id');
    }
}
