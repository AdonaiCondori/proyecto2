@extends('cliente.layouts.master')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="row no-gutters">
                    <div class="col-md-5">
                        @if ($producto->imagen)
                        <img src="{{ asset($producto->imagen) }}" class="card-img" alt="{{ $producto->nombre }}">
                        @else
                        <img src="{{ asset('images/default-product.jpg') }}" class="card-img" alt="Imagen por defecto">
                        @endif
                    </div>
                    <div class="col-md-7">
                        <div class="card-body">
                            <h3 class="card-title">{{ $producto->nombre }}</h3>
                            <h5 class="card-subtitle mb-2 text-muted">Por: {{ $producto->autor }}</h5>
                            <p class="card-text"><strong>Editorial:</strong> {{ $producto->editorial }}</p>
                            <p class="card-text">{{ $producto->descripcion }}</p>
                            @if($producto->descuento)
                            <p class="card-text">
                                <span class="text-danger"><del>Bs. {{ number_format($producto->precio, 2) }}</del></span>
                                <span class="text-success">Bs. {{ number_format($producto->precio - ($producto->precio * $producto->descuento / 100), 2) }}</span>
                            </p>
                            @else
                            <p class="card-text text-success">Bs. {{ number_format($producto->precio, 2) }}</p>
                            @endif
                            <p class="card-text"><strong>Stock disponible:</strong> {{ $producto->stock }}</p>
                            <form action="{{ route('cliente.carrito.agregar', $producto->id_producto) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-dark btn-sm btn-block">
                                    <i class="fas fa-cart-plus"></i> comprar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted text-center">
                    <small>Última actualización: {{ $producto->updated_at->format('d/m/Y') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
