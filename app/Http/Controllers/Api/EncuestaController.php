<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluado;
use Illuminate\Http\Request;
use App\Models\Respuesta;
use App\Models\Pregunta;
use App\Services\EncuestaService;
use App\User;

class EncuestaController extends Controller
{
    public function getIndex(Request $request, $id = "")
    {
        if ($id) {
            $data = Pregunta::where('encuesta_id', $id)->orderBy('orden')->get();
            foreach ($data as $k => $d) {
                $data[$k]->respuesta_valores = json_decode($data[$k]->respuesta_valores);
                $data[$k]->pregunta_activadora_cond = json_decode($data[$k]->pregunta_activadora_cond);
            }
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            $data = Evaluado::where('usuario_id', auth()->user()->id)->get()->load('encuesta')->load('user');
            return response()->json(['success' => true, 'data' => $data]);
        }
    }

    public function postCreate(Request $request)
    {
        try {
            // $encuesta_id = 1;
            $evaluado = new Evaluado;
            $evaluado->nro_identificacion = $request->encuesta[array_key_first($request->encuesta)];
            $evaluado->fecha = date('Y-m-d H:i:s');
            $evaluado->encuesta_id = $request->encuesta_id;
            $evaluado->usuario_id = auth()->user()->id;
            $evaluado->save();

            $idEvaluado = $evaluado->id;

            $bulkData = [];
            foreach ($request->encuesta as $e => $v) {
                if (is_array($v)) {
                    foreach ($v as $x) {
                        $k = explode('=>', $x);
                        $bulkData[] = [
                            'encuesta_id' => $request->encuesta_id,
                            'evaluado_id' => $idEvaluado,
                            'pregunta_id' => $e,
                            'codigo' => $k[0],
                            'respuesta' => $k[1]
                        ];
                    }
                } else {
                    $k = explode('=>', $v);
                    if (count($k) > 1) {
                        $codigo = $k[0];
                        $respuesta = $k[1];
                    } else {
                        $codigo = '';
                        $respuesta = $k[0];
                    }
                    $bulkData[] = [
                        'encuesta_id' => $request->encuesta_id,
                        'evaluado_id' => $idEvaluado,
                        'pregunta_id' => $e,
                        'codigo' => $codigo,
                        'respuesta' => $respuesta
                    ];
                }
            }

            Respuesta::insert($bulkData);

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function getValidateEntity(Request $request, $id)
    {
        try {
            $encuestaId = $id;
            $identificador = $request->id;

            return response()->json(['success' => true, 'valid' => EncuestaService::validateIdentificador($encuestaId, $identificador)]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
