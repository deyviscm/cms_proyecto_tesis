<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;

class FileController extends Controller
{
	const PATH = "/temps";
	/*
	 Asegurarse de configurar:
	   - post_max_size
	   - upload_max_filesize
	 * */

	public function guardarArchivosUp(Request $request)
	{
		$archivos = $request->allFiles();

		$archivosResp = array();
		$url_path = '';
		try{
			if ($archivos != nuLl) {

				foreach ($archivos as $index => $archivo)
				{
					$nombreOriginal = $archivo->getClientOriginalName();
					if(is_null($nombreOriginal)) throw new \Exception("No se pudo extraer el nombre original del archivo");
					$extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
					$nombreArchivo = $this->limpiarNombreArchivos(pathinfo($nombreOriginal, PATHINFO_FILENAME))."_".uniqid().(strlen($extension) === 0 ? '' : "." . $extension);
					// $archivo->storeAs(self::PATH, $nombreArchivo);
					$file = public_path('temps/').$nombreArchivo;
					// eliminar file
					if(file_exists($file)) unlink($file);
					copy($archivo, $file);

					array_push($archivosResp, $nombreArchivo);

					$url_path = url('temps'). '/'. $nombreArchivo;
				}

				return response()->json(['success' => true, 'data' => $archivosResp, 'nombre' => $nombreOriginal, 'url_path' => $url_path], 200); /*, 'name' => 'sasas'*/
			}else{
				return response()->json(['success' => false, 'error' => 'No se envió ninguna archivo'], 200);
			}

		}catch (\Exception $e){
			Log::error($e);
			return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
		}
	}
	
	public function limpiarNombreArchivos($filename)
	{
		$filename = str_replace('#', '', $filename);
		$filename = str_replace('%', '°', $filename);
		$filename = str_replace('&', 'and', $filename);
		$filename = str_replace('+', 'm', $filename);
		return $filename;
	}
}
?>