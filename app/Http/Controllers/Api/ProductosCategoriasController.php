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
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductosCategoriasController extends Controller
{
	public function getIndex(Request $request)
	{
		try {
			$results = $request->results ? $request->results : 10;
			$page = (isset($request->page) && $request->page != '') ? $request->page : 1;
			$items = (isset($request->items) && $request->items != '') ? $request->items : 10;
			$filter = [];
			$filter['categoria'] = $request->categoria ? $request->categoria : '';

			$data = ProductosCategoriasService::items($results, 'id', 'desc', $filter);
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
			$data = ProductosCategoriasService::getCategoria($id);
			$url_web = config('app.url_web');

			$url_img_web = config('app.path_documentos_web').'/public/images/categorias/'.$data->imagen;
			$file = public_path('temps/').$data->imagen;
			// eliminar file
			if(file_exists($file)) unlink($file);
			copy($url_img_web, $file);
			$data->url_imagen = url('temps'). '/'. $data->imagen;

			return response()->json(['success' => true, 'data' => $data]);
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
		$request->validate([
			'nombre' => 'required',
			'imagen' => 'required',
		]);

		try {
			DB::beginTransaction();
			$data['nombre']  = $request->nombre;
			$data['url']  = $this->seo_friendly_url($request->nombre);
			$v_url = ProductosCategoriasService::url_exists('', $data['url']);
			// validar url
			if($v_url > 0){
				throw new \ErrorException('El nombre de la categoria ya existe.');
			}

			$extension = pathinfo($request->nombre_archivo, PATHINFO_EXTENSION);

			$fileName = config('app.path_documentos_web')."/public/images/categorias/{$data['url']}.{$extension}";
			$file = public_path('temps/').$request->nombre_archivo;
			// eliminar file
			if(file_exists($fileName)) unlink($fileName);
			copy($file, $fileName);

			$data['imagen'] = "{$data['url']}.{$extension}";
			$resp = ProductosCategoriasService::createForm($data);
			DB::commit();
			return response()->json(['success' => true]);
		} catch (\Throwable $th) {
			DB::rollBack();
			return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
		}
	}

	public function postUpdate(Request $request)
	{
		$request->validate([
			'id' => 'required',
			'nombre' => 'required',
			'imagen' => 'required',
		]);

		try {
			DB::beginTransaction();
			$id = $request->id;
			$data['nombre']  = $request->nombre;
			$data['url']  = $this->seo_friendly_url($request->nombre);
			$v_url = ProductosCategoriasService::url_exists($id, $data['url']);
			// validar url
			if($v_url > 0){
				throw new \ErrorException('El nombre de la categoria ya existe.');
			}
			$data['imagen']  = $request->imagen;

			if(isset($request->nombre_archivo) && $request->nombre_archivo != ''){
				$extension = pathinfo($request->nombre_archivo, PATHINFO_EXTENSION);

				$fileName = config('app.path_documentos_web')."/public/images/categorias/{$data['url']}.{$extension}";
				$file = public_path('temps/').$request->nombre_archivo;
				// eliminar file
				if(file_exists($fileName)) unlink($fileName);
				copy($file, $fileName);
				$data['imagen'] = "{$data['url']}.{$extension}";
			}
			$resp = ProductosCategoriasService::updateForm($id, $data);
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
			$filter= [];
			$filter['categoria'] = $request->id;
			// verificar productos
			$productos = ProductosService::items(null, 'id', 'asc', $filter);
			if(count($productos) > 0){
				throw new \ErrorException('No es posible eliminar el registro porque cuenta con productos.');
			}
			$dt = ProductosCategoriasService::getCategoria($request->id);
			$resp = ProductosCategoriasService::deleteForm($request->id);
			
			$fileName = config('app.path_documentos_web')."/public/images/categorias/{$dt->imagen}";
			// eliminar file
			if(file_exists($fileName)) unlink($fileName);

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
