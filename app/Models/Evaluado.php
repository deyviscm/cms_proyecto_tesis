<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluado extends Model
{
    public $timestamps = false;

    protected $table = 'evaluado';
    
    protected $fillable = [];

    protected $hidden = [];

    protected $casts = [];

    public function encuesta()
    {
        return $this->hasOne(Encuesta::class, 'id', 'encuesta_id');
    }

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'usuario_id');
    }
    
}
