<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OrdenesComprasService
{
	public static function items($results = 0, $sortField = '', $sortOrder = '', $filter = [])
	{
		$data = DB::table('orden_pedidos as op')
			->join('moneda as m','op.id_moneda', '=', 'm.id')
			->select([
				'op.id',
				'op.nro_orden',
				DB::raw('concat(op.nombre," ",op.apellidos) as cliente'),
				'op.celular',
				'op.email',
				'op.id_moneda',
				'op.subtotal',
				'op.envio_pedido',
				DB::raw('round(op.total,2) as total'),
				'm.simbolo',
				'm.descripcion as desc_moneda',
				'op.calle_direccion',
				'op.numero_direccion',
				'op.referencia_direccion',
				DB::raw('(select concat(dpt.name, " / ", prov.name, " / " ,uds.name) as descripcion from ubigeo_distritos as uds 
					inner join ubigeo_departamentos as dpt on uds.department_id = dpt.id
					inner join ubigeo_provincias as prov on uds.province_id = prov.id
					where uds.id = op.ubigeo) as direccion'),
				DB::raw('date_format(op.fechaIn, "%d/%m/%Y") as fecha_compra'),
				DB::raw('(select count(1) from orden_pedido_detalle where id_op = op.id) as total_productos'),
				DB::raw('(select descripcion from estados_admin est where est.codigo = op.estado_pedido and est.estado=1 limit 1) as desc_estado'),
				DB::raw('(select descripcion from tipos_pagos tp where tp.codigo = op.tipo_pago and tp.estado = 1 limit 1) as tipo_pago_desc'),
			])
			->when($sortField, function ($query) use ($sortField, $sortOrder) {
				return $query->orderBy($sortField, $sortOrder == 'ascend' ? 'asc' : 'desc');
			});
		if($filter['nro_orden'] != ''){
			$data = $data->where('nro_orden', $filter['nro_orden']);
		}
		if($filter['estado'] != ''){
			$data = $data->where('estado_pedido', $filter['estado']);
		}
		if($filter['startDate'] != '' && $filter['endDate']){
			$data = $data->whereRaw("date(op.fechaIn) between '{$filter['startDate']}' and '{$filter['endDate']}'");
		}
		// $total = count($query->get());
		// $query = $query->orderBy('op.id', 'desc')->limit($items)->offset(($page - 1) * $items)->get();
		// return ['data' => $query, 'total' => $total];
		return ((int) $results) > 0 ? $data->paginate($results) : $data->get();
	}

	public static function ordenPedido($idOp){
		return DB::table('orden_pedidos as op')
			->join('moneda as m','op.id_moneda', '=', 'm.id')
			->select(['op.id',
			'op.nombre',
			'op.apellidos',
			'op.nro_orden',
			'op.empresa',
			'op.celular',
			'op.email',
			'op.ubigeo',
			'op.calle_direccion',
			'op.numero_direccion',
			'op.referencia_direccion',
			'op.tipo_comprobante',
			'op.nro_documento',
			'op.tipo_pago',
			'op.id_cuenta_bancaria',
			'op.imagen_tranferencia',
			'op.id_moneda',
			'op.subtotal',
			'op.envio_pedido',
			'op.estado_pedido',
			'op.total',
			'op.idcliente',
			'op.fechaIn',
			'm.simbolo',
			'm.descripcion as desc_moneda',
			DB::raw('date_format(op.fechaIn, "%d/%m/%Y") as fecha_compra'),
			DB::raw('(select count(1) from orden_pedido_detalle where id_op = op.id) as total_productos'),
			DB::raw('(select descripcion from estados_admin est where est.codigo = op.estado_pedido and est.estado=1 limit 1) as desc_estado'),
			DB::raw('(select descripcion from tipos_pagos tp where tp.codigo = op.tipo_pago and tp.estado = 1 limit 1) as tipo_pago_desc'),
		])
		->where('op.id', $idOp)
		->first();
	}

	public static function ordenPedidoDetalle($idOp){
		return DB::table('orden_pedido_detalle as od')
			->join('productos as p','od.id_producto', '=', 'p.id')
			->select(['od.id_d',
			'od.id_producto',
			'od.producto',
			'od.precio_unitario',
			'od.cantidad',
			'od.subtotal',
			'od.total',
			'p.categoria_id',
			DB::raw('(select descripcion from moneda m where m.id = od.id_moneda and m.estado=1 limit 1) as desc_moneda'),
			DB::raw('(select simbolo from moneda m where m.id = od.id_moneda and m.estado=1 limit 1) as moneda_simbolo'),
			DB::raw('(select url from productos_imagen img where img.id_producto = p.id and img.principal=1 and img.estado=1 limit 1) as url_producto'),
		])
		->where('od.id_op', $idOp)
		->get();
	}

	public static function updateForm($id, $data){
		return DB::table('orden_pedidos')
				->where('id', $id)
				->update($data);
	}

	public static function graficasVentas($wh)
	{
		$data = DB::table('orden_pedidos as op')
			->join('moneda as m','op.id_moneda', '=', 'm.id')
			->select([
				DB::raw('date_format(op.fechaIn, "%Y-%m-%d") as fecha'),
				DB::raw('round(sum(op.total),2) as total'),
			])
			->whereRaw("op.estado_pedido in (1, 2, 3)");
		if($wh['desde'] != '' && $wh['hasta'] != ''){
			$data = $data->whereRaw("date(op.fechaIn) between '{$wh['desde']}' and '{$wh['hasta']}'");
		}
		$data = $data->groupBy(DB::raw('date_format(op.fechaIn, "%Y-%m-%d")'))->get();
		return $data;
	}

	public static function graficasVentasMensual($wh)
	{
		$data = DB::table('orden_pedidos as op')
			->join('moneda as m','op.id_moneda', '=', 'm.id')
			->select([
				DB::raw('date_format(op.fechaIn, "%Y-%m") as fecha'),
				DB::raw('round(sum(op.total),2) as total'),
			])
			->whereRaw("op.estado_pedido in (1, 2, 3)");
		if($wh['desde'] != '' && $wh['hasta'] != ''){
			$data = $data->whereRaw("date(op.fechaIn) between '{$wh['desde']}' and '{$wh['hasta']}'");
		}
		$data = $data->groupBy(DB::raw('date_format(op.fechaIn, "%Y-%m")'))->get();
		return $data;
	}

	public static function graficasVentasAnios($wh)
	{
		$data = DB::table('orden_pedidos as op')
			->join('moneda as m','op.id_moneda', '=', 'm.id')
			->select([
				DB::raw('date_format(op.fechaIn, "%Y") as fecha'),
				DB::raw('round(sum(op.total),2) as total'),
			])
			->whereRaw("op.estado_pedido in (1, 2, 3)");
		if($wh['desde'] != '' && $wh['hasta'] != ''){
			$data = $data->whereRaw("date(op.fechaIn) between '{$wh['desde']}' and '{$wh['hasta']}'");
		}
		$data = $data->groupBy(DB::raw('date_format(op.fechaIn, "%Y")'))->get();
		return $data;
	}

}
