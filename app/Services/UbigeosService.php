<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class UbigeosService
{
	public static function getUbigeo(){
		$resp = DB::table('ubigeo_distritos as uds')
			->join('ubigeo_departamentos as dpt', 'uds.department_id', '=', 'dpt.id')
			->join('ubigeo_provincias as prov', 'uds.province_id', '=', 'prov.id')
			->select([
				'uds.id as ubigeo',
				DB::raw("concat(dpt.name, ' / ', prov.name, ' / ' ,uds.name) as descripcion")
			])
			->get();
		return $resp;
	}
}
