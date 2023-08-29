<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function getIndex(Request $request)
    {
        $menu = [];

        $perfil = auth()->user()->perfil_id;
        
        switch ($perfil) {
            case '1':
                $menu = [
                    ['url' => '/home', 'name' => 'Home', 'icon' => 'home'],
                    ['url' => '/usuario', 'name' => 'Usuario', 'icon' => 'user'],
                    ['url' => '/ordenes-compras', 'name' => 'Orden de Ventas', 'icon' => 'shopping-cart'],
                    ['url' => '/categorias', 'name' => 'Categorias', 'icon' => 'folder'],
                    ['url' => '/productos', 'name' => 'Productos', 'icon' => 'folder'],
                ];
                break;

            case '2':
                $menu = [];
                break;

            case '3':
                $menu = [];
                break;
            
            case '4':
                $menu = [];
                break;
        }

        return response()->json(['success' => true, 'data' => $menu]);
    }
}
