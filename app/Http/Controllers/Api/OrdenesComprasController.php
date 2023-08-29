<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Services\UsuarioService;
use App\Services\OrdenesComprasService;
use App\Services\PerfilService;
use App\Services\EstadosAdmServices;
use App\Services\UbigeosService;
use App\Services\TiposPagosService;
use App\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrdenesComprasController extends Controller
{
	public function getIndex(Request $request)
	{
		try {
			$results = $request->results ? $request->results : 10;
			$page = (isset($request->page) && $request->page != '') ? $request->page : 1;
			$items = (isset($request->items) && $request->items != '') ? $request->items : 10;
			$filter = [];
			
			$filter['nro_orden'] = $request->nro_orden ? str_pad($request->nro_orden, 8, '0', STR_PAD_LEFT) : '';
			$filter['estado'] = $request->estado ? $request->estado : '';
			$filter['startDate'] = $request->startDate ? $request->startDate : '';
			$filter['endDate'] = $request->endDate ? $request->endDate : '';

			$data = OrdenesComprasService::items($results, 'id', 'desc', $filter);
			return response()->json(['success' => true, 'data' => $data]);
		} catch (\Throwable $th) {
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function getForm()
	{
		try {
			$data = [
				'perfiles' => PerfilService::items(null, 'id', 'asc'),
				'estados' => EstadosAdmServices::items(null, 'id', 'asc'),
				'ubigeos' => UbigeosService::getUbigeo(),
				'tipoPagos' => TiposPagosService::items(null, 'id', 'asc'),
				'path' => config('app.path_documentos_web'),
				'url_web' => config('app.url_web'),
			];

			return response()->json(['success' => true, 'data' => $data]);
		} catch (\Throwable $th) {
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function getInfo($id)
	{
		try {
			$data = OrdenesComprasService::ordenPedido($id);
			$dataDetalle = OrdenesComprasService::ordenPedidoDetalle($id);
			$url_web = config('app.url_web');
			foreach($dataDetalle as $row){
				$row->url_producto = $url_web.'/public/images/productos/'.$row->url_producto;
				$row->total = number_format($row->total, 2);
			}
			return response()->json(['success' => true, 'data' => $data, 'detalle' => $dataDetalle]);
		} catch (\Throwable $th) {
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function postDescargar(Request $request){
		$id= $request->id;
		$imagen= $request->imagen;
		$url = config('app.path_documentos_web')."/public/images/imagen_transferencia/{$id}/{$imagen}";
		
		$fileName = $imagen;
		$file = public_path('temps/').$fileName;
		// eliminar file
		if(file_exists($file)) unlink($file);
		copy($url, $file);
		return response()->json(['realOath' => $file, 'fileName' => $fileName, 'url' => url('temps'). '/'. $fileName]);
	}
	
	public function postUpdate(Request $request)
	{
		$request->validate([
			'id' => 'required',
		]);

		try {
			DB::beginTransaction();
			$id = $request->id;
			$data= [];
			$data['estado_pedido']  = $request->estado_pedido;
			$resp = OrdenesComprasService::updateForm($id, $data);
			
			$this->enviarCorreo($id);
			DB::commit();
			return response()->json(['success' => true]);
		} catch (\Throwable $th) {
			DB::rollBack();
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function enviarCorreo($id){

		$datos = (array) OrdenesComprasService::ordenPedido($id);
		$productos = OrdenesComprasService::ordenPedidoDetalle($id);
		
		$ubigeo = UbigeosService::getUbigeo();
		$direccion = $ubigeo->where('ubigeo', $datos['ubigeo'])->first();

		$mensaje = '';
		
		if($datos['estado_pedido'] == '2'){ // compra confirmada
			
			$datos['title'] = 'COMPRA CONFIRMADA';
			$datos['subject'] = 'Compra Confirmada OMEGA';

			$mensaje .= "<p>Hola {$datos['nombre']},</p>";
			$mensaje .= "<p>Tu número de orden <b>N° {$datos['nro_orden']}</b> fue aprobado.</p>";
			$mensaje .= "<p>Gracias por tu compra.</br>Tu pago fue aprobado con éxito.</p>";


		}else if($datos['estado_pedido'] == '3'){ // Entregada
			$datos['title'] = 'COMPRA ENTREGADA';
			$datos['subject'] = 'Compra Entregada OMEGA';

			$mensaje .= "<p>Hola {$datos['nombre']},</p>";
			$mensaje .= "<p>Tu compra <b>N° {$datos['nro_orden']}</b> fue entregado con éxito.</p>";
			$mensaje .= "<p>Gracias por tu compra.</p>";

		}else if($datos['estado_pedido'] == '4'){ // anulada
			$datos['title'] = 'COMPRA ANULADA';
			$datos['subject'] = 'Compra Anulada OMEGA';

			$mensaje .= "<p>Tu compra <b>N° {$datos['nro_orden']}</b> fue anulada.</p>";
		}

		$mensaje .= "<table style='' border='0' cellpadding='0' cellspacing='0' width='100%'>";
		$mensaje .= "<tr>";
		$mensaje .= "<td>Nro Compra:</td>";
		$mensaje .= "<td>{$datos['nro_orden']}</td>";
		$mensaje .= "</tr>";
		$mensaje .= "<tr>";
		$mensaje .= "<td>Fecha Compra:</td>";
		$mensaje .= "<td>".date('d/m/Y')."</td>";
		$mensaje .= "</tr>";
		$mensaje .= "<tr>";
		$mensaje .= "<td>Total:</td>";
		$mensaje .= "<td>S/ {$datos['total' ]}</td>";
		$mensaje .= "</tr>";
		$mensaje .= "<tr>";
		$mensaje .= "<td>Dirección:</td>";
		$mensaje .= "<td>{$direccion->descripcion}</td>";
		$mensaje .= "</tr>";
		$mensaje .= "<tr>";
		$mensaje .= "<td>Calle:</td>";
		$mensaje .= "<td>{$datos['calle_direccion']}</td>";
		$mensaje .= "</tr>";
		$mensaje .= "<tr>";
		$mensaje .= "<td>Número:</td>";
		$mensaje .= "<td>{$datos['numero_direccion']}</td>";
		$mensaje .= "</tr>";
		$mensaje .= "<tr>";
		$mensaje .= "<td>Referencia:</td>";
		$mensaje .= "<td>{$datos['referencia_direccion']}</td>";
		$mensaje .= "</tr>";
		$mensaje .= "</table>";

		$url = config('app.url_web');
		$mensaje .= "</br>";
		$mensaje .= "<table  id='fr-table' style='' border='0' cellpadding='0' cellspacing='0' width='100%'>";
		$mensaje .= "<thead>";
		$mensaje .= "<tr>";
		$mensaje .= "<th>Producto</th>";
		$mensaje .= "<th>Descripción</th>";
		$mensaje .= "<th>P.Unitario</th>";
		$mensaje .= "<th>Cantidad</th>";
		$mensaje .= "<th>Total</th>";
		$mensaje .= "</tr>";
		$mensaje .= "</thead>";
		foreach($productos as $row){
			$mensaje .= "<tr>";
			$image = $url.'/public/images/productos/'.$row->url_producto;
			$mensaje .= "<td align='center'><img width='100' src='{$image}'></td>";
			$mensaje .= "<td>{$row->producto}</td>";
			$mensaje .= "<td align='right'>".number_format($row->precio_unitario,2)."</td>";
			$mensaje .= "<td align='center'>{$row->cantidad}</td>";
			$mensaje .= "<td align='right'>".number_format($row->total ,2)."</td>";
			$mensaje .= "</tr>";
		}
		$mensaje .= "</table>";

		$mensaje .= "<p>Gracias por realizar su compra en <a href='{$url}'>http://minimarket.test/</a></p>";

		$mensaje .= "<p>Saludos Cordiales</p>";

		$datos['mensaje'] = $mensaje;

		// Correo Administrador
		$datos['cc'] = (env('EMAIL_ADMIN') != '') ? explode(',', env('EMAIL_ADMIN')) : [];

		$send = Mail::send('mails.template', $datos, function($message) use ($datos){
			$message->to($datos['email'] , $datos['nombre'])->cc($datos['cc'])->subject($datos['subject']);
		});
	}


	public function postRemove(Request $request)
	{
		$request->validate([
			'id' => 'required',
		]);

		try {
			$user = User::find($request->id);
			$user->deleted_at = date('Y-m-d');
			$user->save();

			return response()->json(['success' => true]);
		} catch (\Throwable $th) {
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function postGraficasVentas(Request $request){
		try {
			$data['desde'] = $request->desde;
			$data['hasta'] = $request->hasta;

			$datos = OrdenesComprasService::graficasVentas($data);
			$groupData = [];
			if(count($datos) > 0){
				foreach($datos as $row){
					$groupData[$row->fecha] = $row;
				}
			}
			// print_r($groupData);
			$dataGr = [];
			$currentDate = strtotime($data['desde']);
			$tipoX = [];
			while( $currentDate <= strtotime($data['hasta'])){
				$fecha = date('Y-m-d', $currentDate);
				if(!isset($groupData[$fecha])){
					$dataGr[] = (object) ['name' => $fecha, 'y' => 0];
				}else{
					// print_r($groupData[$fecha]);
					$dataGr[] = (object) ['name' => $groupData[$fecha]->fecha, 'y' => $groupData[$fecha]->total];
				}
				$tipoX[] = $fecha;
				$currentDate = strtotime('+1 day', $currentDate);
				
				
			}

			return response()->json(['success' => true, "data" => $dataGr, 'tipoX' => $tipoX]);
		} catch (\Throwable $th) {
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function postGraficasVentasMensual(Request $request){
		try {
			$data['desde'] = $request->desde;
			$data['hasta'] = $request->hasta;

			$datos = OrdenesComprasService::graficasVentasMensual($data);
			$groupData = [];
			if(count($datos) > 0){
				foreach($datos as $row){
					$groupData[$row->fecha] = $row;
				}
			}
			// $dataGr = [];
			// $currentDate = strtotime($data['desde']);
			// // $meses = array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
			// $tipoX = [];
			// foreach($meses as $key => $row){
			// 	if(!isset($groupData[$key])){
			// 		$dataGr[] = (object) ['name' => $row, 'y' => 0];
			// 	}else{
			// 		$dataGr[] = (object) ['name' => $row, 'y' => $groupData[$key]->total];
			// 	}
			// 	$tipoX[] = $row;
			// }

			$dataGr = [];
			$currentDate = strtotime($data['desde']);

			while( $currentDate <= strtotime($data['hasta'])){
				$fecha = date('Y-m', $currentDate);
				if(!isset($groupData[$fecha])){
					$dataGr[] = (object) ['name' => $fecha, 'y' => 0];
				}else{
					// print_r($groupData[$fecha]);
					$dataGr[] = (object) ['name' => $groupData[$fecha]->fecha, 'y' => $groupData[$fecha]->total];
				}
				$tipoX[] = $fecha;
				$currentDate = strtotime('+1 month', $currentDate);
				
				
			}
			return response()->json(['success' => true, "data" => $dataGr, 'tipoX' => $tipoX]);
		} catch (\Throwable $th) {
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function postGraficasVentasAnios(Request $request){
		try {
			$data['desde'] = $request->desde;
			$data['hasta'] = $request->hasta;

			$datos = OrdenesComprasService::graficasVentasAnios($data);
			$groupData = [];
			if(count($datos) > 0){
				foreach($datos as $row){
					$groupData[$row->fecha] = $row;
				}
			}

			$dataGr = [];
			$currentDate = strtotime($data['desde']);

			while( $currentDate <= strtotime($data['hasta'])){
				$fecha = date('Y', $currentDate);
				if(!isset($groupData[$fecha])){
					$dataGr[] = (object) ['name' => $fecha, 'y' => 0];
				}else{
					// print_r($groupData[$fecha]);
					$dataGr[] = (object) ['name' => $groupData[$fecha]->fecha, 'y' => $groupData[$fecha]->total];
				}
				$tipoX[] = $fecha;
				$currentDate = strtotime('+1 year', $currentDate);
				
				
			}
			return response()->json(['success' => true, "data" => $dataGr, 'tipoX' => $tipoX]);
		} catch (\Throwable $th) {
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function postDescargarExcel(Request $request){

		$filter = [];
			
		$filter['nro_orden'] = $request->nro_orden ? str_pad($request->nro_orden, 8, '0', STR_PAD_LEFT) : '';
		$filter['estado'] = $request->estado ? $request->estado : '';
		$filter['startDate'] = $request->startDate ? $request->startDate : '';
		$filter['endDate'] = $request->endDate ? $request->endDate : '';

		$arrayColumnas = [
			'NRO ORDEN',
			'NOMBRES',
			'CELULAR',
			'EMAIL',
			'DIRECCION',
			'CALLE',
			'NUMERO',
			'REFERENCIA',
			'TIPO PAGO',
			'CANT. PRODUCTOS',
			'MONEDA',
			'TOTAL',
			'ESTADO',
			'FECHA REGISTRO',
		];
		$spreadsheet = new Spreadsheet();
		$spreadsheet->getProperties()->setCreator('Sistemas');
		$spreadsheet->setActiveSheetIndex(0);
		$spreadsheet->getActiveSheet()->setTitle("ORDENES");
		$spreadsheet->getActiveSheet()->fromArray($arrayColumnas, null, 'A1');

		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(35);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(35);
		$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(35);
		$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(20);
		$i = 2;

		$data = OrdenesComprasService::items(0, 'id', 'desc', $filter);
		// print_r($data);exit();
		if(count($data) > 0){
			foreach($data as $row){
				$spreadsheet->getActiveSheet()->setCellValue("A{$i}", $row->nro_orden);
				$spreadsheet->getActiveSheet()->setCellValue("B{$i}", $row->cliente);
				$spreadsheet->getActiveSheet()->setCellValue("C{$i}", $row->celular);
				$spreadsheet->getActiveSheet()->setCellValue("D{$i}", $row->email);
				$spreadsheet->getActiveSheet()->setCellValue("E{$i}", $row->direccion);
				$spreadsheet->getActiveSheet()->setCellValue("F{$i}", $row->calle_direccion);
				$spreadsheet->getActiveSheet()->setCellValue("G{$i}", $row->numero_direccion);
				$spreadsheet->getActiveSheet()->setCellValue("H{$i}", $row->referencia_direccion);
				$spreadsheet->getActiveSheet()->setCellValue("I{$i}", $row->tipo_pago_desc);
				$spreadsheet->getActiveSheet()->setCellValue("J{$i}", $row->total_productos);
				$spreadsheet->getActiveSheet()->setCellValue("K{$i}", $row->simbolo);
				$spreadsheet->getActiveSheet()->setCellValue("L{$i}", $row->total);
				$spreadsheet->getActiveSheet()->setCellValue("M{$i}", $row->desc_estado);
				$spreadsheet->getActiveSheet()->setCellValue("N{$i}", $row->fecha_compra);
				$i++;
			}
		}
        $i++;
		// Crear el objeto Writer para generar el archivo
        $writer = new Xlsx($spreadsheet);
		$fileName = 'orden_ventas.xlsx';
		$file = public_path('temps/').$fileName;
		if(file_exists($file)) unlink($file);
        $writer->save($file);
        //exit();
		return response()->json(['realOath' => $file, 'fileName' => $fileName, 'url' => url('temps'). '/'. $fileName]);
    }
}
