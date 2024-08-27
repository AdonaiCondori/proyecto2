<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Carrito; // Asumiendo que tienes un modelo para el carrito

class ClienteProductosController extends Controller
{
    // Mostrar todas las categorías y productos de la primera categoría
    public function index()
    {
        $categorias = Categoria::all();
        $categoria = $categorias->first();
        $productos = $categoria ? $categoria->productos : collect();

        return view('cliente.layouts.master', compact('categorias', 'categoria', 'productos'));
    }

    // Mostrar los productos de una categoría específica
    public function mostrarCategoria($id_categoria)
    {
        $categorias = Categoria::all();
        $categoria = Categoria::findOrFail($id_categoria);
        $productos = $categoria->productos;

        return view('cliente.layouts.master', compact('categorias', 'categoria', 'productos'));
    }

    // Agregar un producto al carrito
    public function agregarAlCarrito(Request $request, $id_producto)
    {
        $producto = Producto::findOrFail($id_producto);

        // Aquí puedes agregar la lógica para agregar el producto al carrito
        // Asumiendo que tienes un modelo Carrito con una relación belongsToMany con Producto
        $carrito = session()->get('carrito', []);
        $carrito[$id_producto] = [
            'nombre' => $producto->nombre,
            'cantidad' => isset($carrito[$id_producto]) ? $carrito[$id_producto]['cantidad'] + 1 : 1,
            'precio' => $producto->precio,
            'imagen' => $producto->imagen
        ];

        session()->put('carrito', $carrito);

        return redirect()->back()->with('success', 'Producto añadido al carrito');
    }

    // Mostrar el carrito
    public function mostrarCarrito()
    {
        $carrito = session()->get('carrito', []);
        return view('cliente.carrito.index', compact('carrito'));
    }
}
