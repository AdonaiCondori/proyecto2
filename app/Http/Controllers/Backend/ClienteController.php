<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Promocion;

class ClienteController extends Controller
{
    public function dashboard() {
        // Recuperar promociones que aún están vigentes
        $promociones = Promocion::with('producto')->where('fecha_fin', '>=', now())->get();
    
        $productos = Producto::paginate(8);
        
        // Recuperar todas las categorías
        $categorias = Categoria::all();
    
        // Pasar los datos a la vista
        return view('cliente.dashboard', compact('promociones', 'productos', 'categorias'));
    }
    public function promocion() {
        // Recuperar promociones vigentes
        $promociones = Promocion::with('producto')->where('fecha_fin', '>=', now())->get();
        return view('cliente.profile.promocion', compact('promociones'));
    }
    public function login() {
        return view('cliente.auth.login');
    }
    public function buscar(Request $request) {
        // Obtener el término de búsqueda desde el formulario
        $query = $request->input('search');
    
        // Consultar la base de datos para encontrar productos que coincidan con el término de búsqueda en cualquier campo relevante
        $productos = Producto::where('nombre', 'LIKE', "%{$query}%")
            ->orWhere('autor', 'LIKE', "%{$query}%")
            ->orWhere('editorial', 'LIKE', "%{$query}%")
            ->orWhere('breve_descripcion', 'LIKE', "%{$query}%")
            ->orWhere('descripcion', 'LIKE', "%{$query}%")
            ->orWhere('precio', 'LIKE', "%{$query}%")  // Si quieres incluir el precio en la búsqueda
            ->get();
    
        // Retornar la vista con los resultados de búsqueda
        return view('cliente.layouts.index', compact('productos'));
    }
}

