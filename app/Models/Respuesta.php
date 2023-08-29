<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    public $timestamps = false;
    
    protected $table = 'respuesta';
    
    protected $fillable = [];

    protected $hidden = [];

    protected $casts = [];
    
}
