<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\DetalleFactura;
use App\Models\Factura;
use Illuminate\Http\Request;

class CliProducController extends Controller
{
   
    public function verCarrito()
    {
        // Obtener la factura pendiente del cliente actual
        $factura = Factura::where('id_cliente', auth()->id())
            ->where('estado_factura', 'pendiente')
            ->first();

        if (!$factura) {
            return redirect()->route('cliente.layouts.index')->with('error', 'No hay productos en el carrito.');
        }

        // Agrupar los detalles por producto para evitar duplicados y contar la cantidad total
        $detalles_agrupados = $factura->detalles->groupBy('id_producto');
        $detalles = collect();

        foreach ($detalles_agrupados as $detalles_producto) {
            $primer_detalle = $detalles_producto->first();
            $total = $detalles_producto->sum('cantidad');
            $primer_detalle->cantidad = $total;
            $detalles->add($primer_detalle);
        }

        return view('cliente.layouts.carrito', compact('detalles', 'factura'));
    }





    public function cancelarCompra(Request $request)
    {
        $id_cliente = auth()->id();

        // Obtener y eliminar la factura pendiente del cliente actual
        $factura = Factura::where('id_cliente', $id_cliente)
            ->where('estado_factura', 'pendiente')
            ->first();

        if ($factura) {
            // Eliminar los detalles de la factura si es necesario
            $factura->detalles()->delete();
            // Eliminar la factura
            $factura->delete();
        }

        // Redirigir a la página principal de productos con mensaje de éxito
        return redirect()->route('cliente.layouts.index')->with('success', 'Compra cancelada correctamente.');
    }

    public function listarPedidos()
    {
        $id_cliente = auth()->id();

        // Obtener todos los pedidos realizados por el cliente
        $pedidos = Factura::where('id_cliente', $id_cliente)
            ->where('estado_factura', '!=', 'pendiente') // Mostrar solo pedidos completados o anulados, ajusta según tu lógica
            ->orderByDesc('fecha')
            ->get();

        return view('cliente.layouts.pedidos', compact('pedidos'));
    }

    public function detalleFactura($id_factura)
    {
        // Obtener la factura por su ID
        $factura = Factura::findOrFail($id_factura);

        // Retornar la vista de detalle con la factura obtenida
        return view('cliente.layouts.detalle', compact('factura'));
    }

    public function eliminarDelCarrito($id_detalle)
    {
        // Encontrar el detalle de factura por su ID
        $detalle = DetalleFactura::findOrFail($id_detalle);

        // Eliminar el detalle de factura
        $detalle->delete();

        // Redirigir de vuelta a la página del carrito con un mensaje
        return redirect()->route('cliente.carrito.ver')->with('success', 'Producto eliminado del carrito correctamente.');
    }

    
}
