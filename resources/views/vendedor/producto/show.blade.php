@extends('vendedor.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mb-4 shadow-lg border-0">
                <div class="row g-0">
                    <!-- Product Image -->
                    <div class="col-md-4">
                        <img src="{{ asset($producto->imagen) }}" class="img-fluid rounded-start" alt="{{ $producto->nombre }}" style="object-fit: cover; height: 100%;">
                    </div>

                    <!-- Product Details -->
                    <div class="col-md-8">
                        <div class="card-body">
                            <h3 class="card-title mb-3">{{ $producto->nombre }}</h3>
                            <p class="card-text"><strong>Autor:</strong> {{ $producto->autor }}</p>
                            <p class="card-text"><strong>Editorial:</strong> {{ $producto->editorial }}</p>
                            <p class="card-text"><strong>Breve Descripción:</strong> {{ $producto->breve_descripcion }}</p>
                            <p class="card-text"><strong>Descripción:</strong> {{ $producto->descripcion }}</p>
                            
                            <hr>

                            <div class="row">
                                <!-- Pricing -->
                                <div class="col-md-6">
                                    <p class="card-text"><strong>Precio de Compra:</strong> Bs.{{ $producto->precio_compra }}</p>
                                    <p class="card-text"><strong>Precio de Venta:</strong> <span class="text-success">Bs.{{ $producto->precio }}</span></p>
                                    <p class="card-text"><strong>Descuento:</strong> <span class="text-danger">Bs.{{ $producto->descuento }}</span></p>
                                    <p class="card-text"><strong>Total Venta:</strong> <span class="text-primary">Bs.{{ $producto->total_venta }}</span></p>
                                </div>

                                <!-- Stock and Status -->
                                <div class="col-md-6">
                                    <p class="card-text"><strong>Stock Disponible:</strong> 
                                        <span class="{{ $producto->stock > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $producto->stock }}
                                        </span>
                                    </p>
                                    <p class="card-text"><strong>Estado:</strong> 
                                        <span class="{{ $producto->estado == 'activo' ? 'text-success' : 'text-danger' }}">
                                            {{ ucfirst($producto->estado) }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <!-- Timestamps -->
                            <p class="card-text"><strong>Fecha de Creación:</strong> {{ $producto->created_at->format('d/m/Y H:i') }}</p>
                            <p class="card-text"><strong>Última Actualización:</strong> {{ $producto->updated_at->format('d/m/Y H:i') }}</p>
                            
                            <!-- Back Button -->
                            <a href="{{ route('vendedor.producto.index') }}" class="btn btn-secondary mt-3">Volver a la lista</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
