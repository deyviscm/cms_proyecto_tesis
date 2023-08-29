<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class EncuestaService
{
    public static function listPending()
    {
        $data = DB::table('evaluado')
            ->selectRaw('distinct(encuesta_id), encuesta.google_sheet, encuesta.google_sheet_min')
            ->join('encuesta', 'evaluado.encuesta_id', '=', 'encuesta.id')
            // ->join('users', 'evaluado.usuario_id', '=', 'users.id')
            // ->where('anulada', 0)
            ->whereNotNull('encuesta_id')
            ->whereNull('fecha_sincronizacion');
        return $data->get();
    }

    
    public static function listCategorias($encuestaId)
    {
        $data = DB::table('pregunta')
            ->selectRaw('distinct categoria_id, categoria.codigo, categoria.nombre')
            ->join('categoria', 'pregunta.categoria_id', '=', 'categoria.id')
            ->where('pregunta.encuesta_id', $encuestaId);
        return $data->get();
    }

    public static function listPreguntaRespuestaCategoria($categoriaId, $encuestaId)
    {
        $data = DB::select("SELECT 
            ev.nro_identificacion, p.codigo_externo, r.respuesta 
            FROM encuesta_pregunta ep
            INNER JOIN encuesta e ON ep.encuesta_id = e.id
            INNER JOIN pregunta p ON ep.pregunta_id = p.id
            INNER JOIN respuesta r ON (r.pregunta_id = p.id AND r.encuesta_id = e.id)
            INNER JOIN evaluado ev ON r.evaluado_id = ev.id
            WHERE ep.fecha_eliminado IS NULL
            AND p.fecha_eliminado IS NULL
            AND e.fecha_eliminado IS NULL 
            AND ev.anulada = 0
            AND ep.encuesta_id = ?
            AND p.categoria_id = ?", [$encuestaId, $categoriaId]);
        return $data;
    }

    public static function listPreguntaRespuesta($encuestaId, $infoPersonal)
    {
        $data = DB::select("SELECT 
            ev.nro_identificacion, p.codigo_externo, r.respuesta 
            FROM encuesta_pregunta ep
            INNER JOIN encuesta e ON ep.encuesta_id = e.id
            INNER JOIN pregunta p ON ep.pregunta_id = p.id
            INNER JOIN respuesta r ON (r.pregunta_id = p.id AND r.encuesta_id = e.id)
            INNER JOIN evaluado ev ON r.evaluado_id = ev.id
            WHERE ep.fecha_eliminado IS NULL
            AND p.fecha_eliminado IS NULL
            AND e.fecha_eliminado IS NULL 
            AND ev.anulada = 0
            AND ep.encuesta_id = ?
            AND p.info_personal = ?", [$encuestaId, $infoPersonal]);
        return $data;
    }

    public static function validateIdentificador($encuestaId, $identificador)
    {
        $data = DB::select("SELECT count(*) as total
            FROM evaluado ev
            INNER JOIN encuesta e ON ev.encuesta_id = e.id
            WHERE e.fecha_eliminado IS NULL 
            AND ev.anulada = 0
            AND ev.encuesta_id = ?
            AND ev.nro_identificacion = ?", [$encuestaId, $identificador]);
        return $data ? ($data[0]->total == 0 ? true : false) : false;
    }

    public static function listPreguntaCategoria($encuestaId)
    {
        $data = DB::select("SELECT 
            p.codigo_externo, p.nombre, c.nombre AS cat_nombre
            FROM encuesta_pregunta ep
            INNER JOIN encuesta e ON ep.encuesta_id = e.id
            INNER JOIN pregunta p ON ep.pregunta_id = p.id
            INNER JOIN categoria c ON p.categoria_id = c.id
            WHERE ep.fecha_eliminado IS NULL
            AND p.fecha_eliminado IS NULL
            AND e.fecha_eliminado IS NULL 
            AND ep.encuesta_id = ?", [$encuestaId]);
        return $data;
    }
}
