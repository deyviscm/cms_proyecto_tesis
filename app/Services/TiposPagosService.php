<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TiposPagosService
{
	public static function items($results = 0, $sortField = '', $sortOrder = ''){
		$data = DB::table('tipos_pagos as tp')
			->select([
				'tp.id_tp as id',
				'tp.codigo',
				'tp.descripcion',
				'tp.estado',
			])
            ->when($sortField, function ($query) use ($sortField, $sortOrder) {
				return $query->orderBy($sortField, $sortOrder == 'ascend' ? 'asc' : 'desc');
			});
		return ((int) $results) > 0 ? $data->paginate($results) : $data->get();
	}
}
