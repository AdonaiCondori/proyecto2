@extends('cliente.layouts.master')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-dark text-white text-center">
            <h4 class="mb-0 py-2">Promociones Especiales</h4>
        </div>
        <div class="card-body">
            @if($promociones->isEmpty())
                <p class="text-center">No hay productos en promoción en este momento.</p>
            @else
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                    @foreach ($promociones as $promocion)
                    <div class="col mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="position-relative">
                                <img src="{{ asset($promocion->producto->imagen ?? 'images/default-product.jpg') }}" class="card-img-top" alt="Imagen del producto">
                                <div class="badge bg-success position-absolute top-0 end-0 m-2">-{{ $promocion->descuento }}%</div>
                            </div>
                            <div class="card-body p-3">
                                <h5 class="card-title">{{ $promocion->producto->nombre }}</h5>
                                <p class="card-text">{{ $promocion->nombre }}</p>
                                <p class="text-muted"><small>{{ $promocion->descripcion }}</small></p>
                                <p class="card-text"><del>Bs.{{ number_format($promocion->producto->precio, 2) }}</del> <strong>Bs.{{ number_format($promocion->producto->precio - ($promocion->producto->precio * $promocion->descuento / 100), 2) }}</strong></p>
                            </div>
                            <div class="card-footer p-2">
                                <form action="{{ route('cliente.carrito.agregar', $promocion->producto->id_producto) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-dark btn-sm btn-block">
                                        <i class="fas fa-cart-plus"></i> Añadir al Carrito
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
