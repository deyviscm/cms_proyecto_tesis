<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MonedaService
{
	public static function items($results = 0, $sortField = '', $sortOrder = ''){
		$data = DB::table('moneda as m')
			->select([
				'm.id',
				'm.descripcion',
			])
            ->when($sortField, function ($query) use ($sortField, $sortOrder) {
				return $query->orderBy($sortField, $sortOrder == 'ascend' ? 'asc' : 'desc');
			});
		return ((int) $results) > 0 ? $data->paginate($results) : $data->get();
	}
}
