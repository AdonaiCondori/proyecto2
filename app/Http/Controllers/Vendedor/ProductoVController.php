<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoVController extends Controller
{
    public function index(Request $request, $categoriaId = null)
    {
        // Fetch all categories
        $categorias = Categoria::all();

        // If a category ID is provided, filter products by that category
        if ($categoriaId) {
            $productos = Producto::where('id_categoria', $categoriaId)->paginate(12);
        } else {
            // Otherwise, fetch all products
            $productos = Producto::paginate(12);
        }

        // Return the view with products and categories
        return view('vendedor.producto.index', compact('productos', 'categorias'));
    }

    public function show($id)
{
    // Fetch the product by its ID
    $producto = Producto::findOrFail($id);

    // Return the view with the product details
    return view('vendedor.producto.show', compact('producto'));
}

}
