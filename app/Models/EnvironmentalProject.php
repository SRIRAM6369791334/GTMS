<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvironmentalProject extends Model
{
    protected $guarded = [];

    public function documents()
    {
        return $this->hasMany(EnvironmentalDocument::class, 'project_id');
    }

    public function activities()
    {
        return $this->hasMany(EnvironmentalActivity::class, 'project_id')->latest();
    }
}
