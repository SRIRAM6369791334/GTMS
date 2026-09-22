<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NatureOfWork extends Model
{
    protected $table = 'nature_of_works';
    protected $fillable = ['name', 'status'];
}
