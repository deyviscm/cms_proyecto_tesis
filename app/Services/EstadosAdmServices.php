<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class EstadosAdmServices
{
    public static function items($results = 0, $sortField = '', $sortOrder = '')
    {
        $data = DB::table('estados_admin as e')
            ->select('e.id', 'e.codigo', 'e.descripcion','e.estado')
            ->where('estado', '1')
            ->when($sortField, function ($query) use ($sortField, $sortOrder) {
                return $query->orderBy($sortField, $sortOrder == 'ascend' ? 'asc' : 'desc');
            });
        return ((int) $results) > 0 ? $data->paginate($results) : $data->get();
    }
}
