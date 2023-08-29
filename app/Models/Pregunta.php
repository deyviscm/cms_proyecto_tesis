<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    public $timestamps = false;
    
    protected $table = 'pregunta';
    
    protected $fillable = [];

    protected $hidden = [];

    protected $casts = [];
    
    public function categoria()
    {
        return $this->hasOne(Categoria::class, 'id', 'categoria_id');
    }
}
