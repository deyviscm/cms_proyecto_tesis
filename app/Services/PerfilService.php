<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PerfilService
{
    public static function items($results = 0, $sortField = '', $sortOrder = '')
    {
        $data = DB::table('perfil')
            ->select('perfil.id', 'perfil.nombre')
            ->whereNull('eliminado')
            ->when($sortField, function ($query) use ($sortField, $sortOrder) {
                return $query->orderBy($sortField, $sortOrder == 'ascend' ? 'asc' : 'desc');
            });
        return ((int) $results) > 0 ? $data->paginate($results) : $data->get();
    }
}
