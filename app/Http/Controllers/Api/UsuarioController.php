<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UsuarioService;
use App\Services\PerfilService;
use App\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function getIndex(Request $request)
    {
        try {
            $results = $request->results ? $request->results : 10;
            $data = UsuarioService::items($results, $request->sortField, $request->sortOrder);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function getForm()
    {
        try {
            $data = [
                'perfiles' => PerfilService::items(null, 'id', 'asc')
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function getInfo($id)
    {
        try {
            $data = User::find($id);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function postCreate(Request $request)
    {
        $request->validate([
            'perfil' => 'required',
            'usuario' => 'required|min:6',
            'nombre' => 'required|min:3',
            'email' => 'email',
            'password' => 'required|confirmed|min:6',
        ]);

        try {
            $user = new User;
            $user->perfil_id = $request->perfil;
            $user->name = $request->nombre;
            $user->user = $request->usuario;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->created_at = date('Y-m-d H:i:s');
            $user->save();

            // throw new \Exception('Lanzo un error');

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
    
    public function postUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'perfil' => 'required',
            'usuario' => 'required|min:6',
            'nombre' => 'required|min:3',
            'email' => 'email',
            'password' => 'confirmed',
        ]);

        try {
            $user = User::find($request->id);
            $user->perfil_id = $request->perfil;
            $user->name = $request->nombre;
            $user->user = $request->usuario;
            $user->email = $request->email;
            $user->updated_at = date('Y-m-d H:i:s');
            if($request->password != ''){
                $user->password = bcrypt($request->password);
            }
            $user->save();

            // throw new \Exception('Lanzo un error');

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
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

}
