<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProductosService
{
	public static function items($results = 0, $sortField = '', $sortOrder = '', $filter = [])
	{
		$data = DB::table('productos as p')
			->leftJoin('moneda as m', 'p.id_moneda','=','m.id')
			->select([
				'p.titulo',
				'p.categoria_id',
				'p.categoria',
				'p.descripcion',
				DB::raw('if(ifnull(p.precio_unitario,"") <> "", round(p.precio_unitario,2), "") as precio_unitario'),
				'p.id_moneda',
				'p.estado_precio',
				'p.url',
				'p.id',
				DB::raw('if(ifnull(p.id_moneda,"") <> "", m.descripcion, "") as moneda_descripcion'),
				DB::raw('if(ifnull(p.id_moneda,"") <> "", m.simbolo, "") as moneda_simbolo'),
				DB::raw('(select pi.url from productos_imagen as pi where pi.id_producto = p.id and pi.principal=1 limit 1) as imagen'),
				DB::raw('(select url from categorias as c where c.id = p.categoria_id) as categoria_url'),
				DB::raw('(select nombre from categorias as c where c.id = p.categoria_id) as categoria_nombre'),
			])
			->when($sortField, function ($query) use ($sortField, $sortOrder) {
				return $query->orderBy($sortField, $sortOrder == 'ascend' ? 'asc' : 'desc');
			});
			
		if(count($filter) > 0){
			if(isset($filter['nombre']) && $filter['nombre'] != ''){
				$data = $data->where('p.titulo', 'like', "%{$filter['nombre']}%");
			}
			if(isset($filter['categoria']) && $filter['categoria'] != ''){
				$data = $data->where('p.categoria_id', "{$filter['categoria']}");
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

	public static function getProducto($idProducto){
		$prod = DB::table('productos as p')
				->leftJoin('moneda as m', 'p.id_moneda','=','m.id')
				->select([
					'p.titulo',
					'p.categoria_id',
					'p.categoria',
					'p.descripcion',
					DB::raw('if(ifnull(p.precio_unitario,"") <> "", round(p.precio_unitario,2), "") as precio_unitario'),
					'p.id_moneda',
					'p.estado_precio',
					DB::raw('(select pi.url from productos_imagen as pi where pi.id_producto = p.id and pi.principal=1 limit 1) as imagen'),
					'p.url',
					'p.id',
					DB::raw('if(ifnull(p.id_moneda,"") <> "", m.descripcion, "") as moneda_descripcion'),
					DB::raw('if(ifnull(p.id_moneda,"") <> "", m.simbolo, "") as moneda_simbolo'),
					
				]);
		return $prod->where('p.id', $idProducto)->first();		

	}

	public static function getProductosImagenes($idProducto){
		return DB::table('productos_imagen')
				->where('id_producto', $idProducto)
				->where('estado', 1)
				->orderBy('principal', 'desc')
				->get();
	}


	public static function url_exists($id, $idCategoria, $url){
		$query = DB::table('productos')
				->where('url', $url)
				->where('categoria_id', $idCategoria);
		if($id != ''){
			$query = $query->where('id', '!=', $id);
		}
		return $query->count();
	}

	public static function updateForm($id, $data){
		return DB::table('productos')
				->where('id', $id)
				->update($data);
	}

	public static function createForm($data){
		return DB::table('productos')
				->insertGetId($data);
	}

	public static function deleteForm($id){
		return DB::table('productos')->where('id', $id)->delete();
	}

	public static function createImageForm($data){
		return DB::table('productos_imagen')
				->insertGetId($data);
	}

	public static function updateImageForm($id, $data){
		return DB::table('productos_imagen')
					->where('id', $id)
					->update($data);
	}

	public static function deleteImageForm($id){
		return DB::table('productos_imagen')->where('id', $id)->delete();
	}
}
