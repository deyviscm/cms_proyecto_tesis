<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProductosCategoriasService
{
	public static function items($results = 0, $sortField = '', $sortOrder = '', $filter = [])
	{
		$data = DB::table('categorias as c')
			->select([
				'c.id',
				'c.nombre',
				'c.imagen',
				'c.url',
            ])
			->when($sortField, function ($query) use ($sortField, $sortOrder) {
				return $query->orderBy($sortField, $sortOrder == 'ascend' ? 'asc' : 'desc');
			});
		if(count($filter) > 0){
			if($filter['categoria'] != ''){
				$data = $data->where('c.nombre', 'like', "%{$filter['categoria']}%");
			}
		}

		return ((int) $results) > 0 ? $data->paginate($results) : $data->get();
	}

	public static function getCategoria($id){
		return DB::table('categorias as c')
			->select(['c.id',
			'c.nombre',
			'c.imagen',
			'c.url',
		])
		->where('c.id', $id)
		->first();
	}

	public static function url_exists($id, $url){
		$query = DB::table('categorias')
				->where('url', $url);
		if($id != ''){
			$query = $query->where('id', '!=', $id);
		}
		return $query->count();
	}

	public static function updateForm($id, $data){
		return DB::table('categorias')
				->where('id', $id)
				->update($data);
	}

	public static function createForm($data){
		return DB::table('categorias')
				->insertGetId($data);
	}

	public static function deleteForm($id){
		return DB::table('categorias')->where('id', $id)->delete();
	}

}
