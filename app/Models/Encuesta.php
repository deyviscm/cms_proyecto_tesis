<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    public $timestamps = false;
    
    protected $table = 'encuesta';
    
    protected $fillable = [];

    protected $hidden = [];

    protected $casts = [];
    
}
