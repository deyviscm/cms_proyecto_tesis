<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UsuarioService;
use App\Services\ProductosCategoriasService;
use App\Services\ProductosService;
use App\Services\PerfilService;
use App\Services\EstadosAdmServices;
use App\Services\UbigeosService;
use App\Services\TiposPagosService;
use App\Services\MonedaService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductosController extends Controller
{
	public function getIndex(Request $request)
	{
		try {
			$results = $request->results ? $request->results : 10;
			$page = (isset($request->page) && $request->page != '') ? $request->page : 1;
			$items = (isset($request->items) && $request->items != '') ? $request->items : 10;
			$filter = [];
			$filter['nombre'] = $request->nombre ? $request->nombre : '';
			$filter['categoria'] = $request->categoria ? $request->categoria : '';

			$data = ProductosService::items($results, 'id', 'desc', $filter);
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
				'categorias' => ProductosCategoriasService::items(null, 'id', 'asc'),
				'moneda' => MonedaService::items(null, 'id', 'asc'),
				'url' => config('app.url_web'),
			];

			return response()->json(['success' => true, 'data' => $data]);
		} catch (\Throwable $th) {
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function getInfo($id)
	{
		try {
			$data = ProductosService::getProducto($id);
			$imagenes = ProductosService::getProductosImagenes($id);

			$url_web = config('app.url_web');

			foreach($imagenes as $row){
				$url_img_web = config('app.path_documentos_web').'/public/images/productos/'.$row->url;
				$file = public_path('temps/').$row->url;
				// eliminar file
				if(file_exists($file)) unlink($file);
				copy($url_img_web, $file);
				$row->url_imagen = url('temps'). '/'. $row->url;
			}

			return response()->json(['success' => true, 'data' => $data, 'imagenes' => $imagenes]);
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
	
	public function postCreate(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'titulo' => 'required',
			'categoria_id' => 'required',
			'detalleImg' => 'required|array|min:1',
			'precio_unitario' =>  ($request->precio_unitario != '') ? 'required|numeric|min:1' : '',
		],[],[
			'detalleImg' => 'Imagenes',
		]
		);
		$validator->validate();
		
		try {
			DB::beginTransaction();
			$data = [];
			$data['titulo']  = $request->titulo;
			$data['categoria_id']  = $request->categoria_id;
			$data['categoria']  = 0;
			$data['descripcion']  = $request->descripcion ? $request->descripcion : '';
			$data['precio_unitario']  = $request->precio_unitario;
			$data['id_moneda']  = $request->id_moneda;
			$data['precio_unitario']  = ($request->precio_unitario != '') ? $request->precio_unitario : null;
			$data['url']  = $this->seo_friendly_url($request->titulo);

			$v_url = ProductosService::url_exists('', $request->categoria_id, $data['url']);
			// validar url
			if($v_url > 0){
				throw new \ErrorException('El nombre del Producto ya existe.');
			}

			$id = ProductosService::createForm($data);
			foreach($request->detalleImg as $row){
				// print_r($row);exit();
				$row = (object) $row;
				if(is_null($row->id) || $row->id == ''){
					$extension = pathinfo($row->url, PATHINFO_EXTENSION);
					$fileName = config('app.path_documentos_web')."/public/images/productos/{$row->url}";
					$file = public_path('temps/').$row->url;
					// eliminar file
					if(file_exists($fileName)) unlink($fileName);
					copy($file, $fileName);

					$img = [];
					$img['id_producto'] = $id;
					$img['descripcion'] = explode('.',$row->url)[0];
					$img['url'] = $row->url;
					$img['principal'] = ($row->principal == true) ? 1 : 0;
					$img['estado'] = 1;
					$resp = ProductosService::createImageForm($img);
				}
			}
			if(count($request->documentos_remove) > 0){
				foreach($request->documentos_remove as $row){
					$row = (object) $row;
					if(!is_null($row->id) && $row->id != ''){
						$extension = pathinfo($row->url, PATHINFO_EXTENSION);
						$fileName = config('app.path_documentos_web')."/public/images/productos/{$row->url}";
						$file = public_path('temps/').$row->url;
						// eliminar file
						if(file_exists($fileName)) unlink($fileName);
						$img = [];
						$resp = ProductosService::deleteImageForm($row->id);
	
					}
				}
			}
			DB::commit();
			return response()->json(['success' => true]);
		} catch (\Throwable $th) {
			DB::rollBack();
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function postUpdate(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'titulo' => 'required',
			'categoria_id' => 'required',
			'detalleImg' => 'required|array|min:1',
			'precio_unitario' =>  ($request->precio_unitario != '') ? 'required|numeric|min:1' : '',
		],[],[
			'detalleImg' => 'Imagenes',
		]
		);
		$validator->validate();
		
		try {
			DB::beginTransaction();
			$id = $request->id;
			$data = [];
			$data['titulo']  = $request->titulo;
			$data['categoria_id']  = $request->categoria_id;
			$data['descripcion']  = $request->descripcion ? $request->descripcion : '';
			$data['precio_unitario']  = $request->precio_unitario;
			$data['id_moneda']  = $request->id_moneda;
			$data['precio_unitario']  = ($request->precio_unitario != '') ? $request->precio_unitario : null;
			$data['url']  = $this->seo_friendly_url($request->titulo);

			$v_url = ProductosService::url_exists($id, $request->categoria_id, $data['url']);
			// validar url
			if($v_url > 0){
				throw new \ErrorException('El nombre del Producto ya existe.');
			}

			foreach($request->detalleImg as $row){
				$row = (object) $row;
				$img = [];
				$img['id_producto'] = $id;
				$img['principal'] = ($row->principal) ? 1 : 0;

				if(is_null($row->id) || $row->id == ''){
					$extension = pathinfo($row->url, PATHINFO_EXTENSION);
					$fileName = config('app.path_documentos_web')."/public/images/productos/{$row->url}";
					$file = public_path('temps/').$row->url;
					// eliminar file
					if(file_exists($fileName)) unlink($fileName);
					copy($file, $fileName);
					$img['descripcion'] = explode('.',$row->url)[0];
					$img['url'] = $row->url;
					$img['estado'] = 1;
					$resp = ProductosService::createImageForm($img);
				}else{
					$resp = ProductosService::updateImageForm($row->id, $img);
				}
			}
			if(count($request->documentos_remove) > 0){
				foreach($request->documentos_remove as $row){
					$row = (object) $row;
					if(!is_null($row->id) && $row->id != ''){
						$extension = pathinfo($row->url, PATHINFO_EXTENSION);
						$fileName = config('app.path_documentos_web')."/public/images/productos/{$row->url}";
						$file = public_path('temps/').$row->url;
						// eliminar file
						if(file_exists($fileName)) unlink($fileName);
						$img = [];
						$resp = ProductosService::deleteImageForm($row->id);
	
					}
				}
			}

			$resp = ProductosService::updateForm($id, $data);
			DB::commit();
			return response()->json(['success' => true]);
		} catch (\Throwable $th) {
			DB::rollBack();
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function postRemove(Request $request)
	{
		$request->validate([
			'id' => 'required',
		]);
		try {
			DB::beginTransaction();
			$resp = ProductosService::deleteForm($request->id);
			$imagenes = ProductosService::getProductosImagenes($request->id);
			foreach($imagenes as $row){
				$row = (object) $row;
				if(!is_null($row->id) && $row->id != ''){
					$extension = pathinfo($row->url, PATHINFO_EXTENSION);
					$fileName = config('app.path_documentos_web')."/public/images/productos/{$row->url}";
					// eliminar file
					if(file_exists($fileName)) unlink($fileName);
					$resp = ProductosService::deleteImageForm($row->id);

				}
			}
			DB::commit();
			return response()->json(['success' => true]);
		} catch (\Throwable $th) {
			DB::rollBack();
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	function seo_friendly_url($string){
		$string = str_replace(array('[\', \']'), '', $string);
		$string = preg_replace('/\[.*\]/U', '', $string);
		$string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
		$string = htmlentities($string, ENT_COMPAT, 'utf-8');
		$string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
		$string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
		return strtolower(trim($string, '-'));
	}

}
