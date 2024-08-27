@extends('vendedor.layouts.master')

@section('content')

<div class="container mt-5">
    <h2>Resumen del Pedido</h2>
    <hr>

    <!-- Información de la factura -->
    <h3>Información de la factura:</h3>
    <p><strong>Fecha:</strong> {{ $factura->fecha }} a las {{ $factura->hora }}</p>
    <p><strong>Total de la factura:</strong> Bs. {{ number_format($factura->total_factura, 2) }}</p>
    @if ($factura->lcliente)
        <p><strong>Cliente:</strong> {{ $factura->lcliente->nombre }} ({{ $factura->nit ?? 'NIT No disponible' }})</p>
    @else
        <p><strong>Cliente:</strong> N/A</p>
    @endif
<p><strong>Vendedor Responsable:</strong> {{ Auth::user()->name }}</p>
    <p><strong>Método de Pago:</strong> {{ ucfirst($factura->metodo_pago) }}</p>
    <p><strong>Estado de la Factura:</strong> {{ ucfirst($factura->estado_factura) }}</p>

    <!-- Detalle del Pedido -->
    <h3>Detalle del Pedido:</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalle_factura as $detalle)
                <tr>
                    <td>{{ $detalle['producto']->descripcion }}</td>
                    <td>Bs. {{ number_format($detalle['producto']->precio, 2) }}</td>
                    <td>{{ $detalle['cantidad'] }}</td>
                    <td>Bs. {{ number_format($detalle['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Información adicional del pedido -->
    <h3>Estado del Pedido:</h3>
    <p><strong>Estado Actual:</strong> {{ ucfirst($factura->pedido_estado) }}</p>
    @if ($factura->pedido_estado == 'procesando')
        <p>El pedido está siendo preparado para el envío.</p>
    @elseif ($factura->pedido_estado == 'enviado')
        <p>El pedido ha sido enviado y está en camino al cliente.</p>
    @elseif ($factura->pedido_estado == 'entregado')
        <p>El pedido ha sido entregado al cliente.</p>
    @else
        <p>Estado del pedido desconocido.</p>
    @endif

    <!-- Botones de acción -->
    <div class="mt-4">
        <form action="{{ route('vendedor.carrito.nuevoPedido') }}" method="GET" class="d-inline">
            <button type="submit" class="btn btn-primary">Realizar Nueva Venta</button>
        </form>
        <a href="{{ route('vendedor.carrito.index') }}" class="btn btn-secondary">Volver a Facturas</a>
    </div>
</div>

@endsection
