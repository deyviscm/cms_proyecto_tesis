<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class UsuarioService
{
    public static function items($results = 0, $sortField = '', $sortOrder = '')
    {
        $data = DB::table('users')
            ->select('users.id', 'users.user', 'perfil.id as perfil_id', 'perfil.nombre as perfil_nombre')
            ->join('perfil', 'users.perfil_id', '=', 'perfil.id')
            ->whereNull('deleted_at')
            ->when($sortField, function ($query) use ($sortField, $sortOrder) {
                return $query->orderBy($sortField, $sortOrder == 'ascend' ? 'asc' : 'desc');
            });
        return ((int) $results) > 0 ? $data->paginate($results) : $data->get();
    }
}
