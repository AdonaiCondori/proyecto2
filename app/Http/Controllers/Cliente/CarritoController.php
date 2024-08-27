<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\DetalleFactura;
use App\Models\Factura;
use App\Models\PedidoWeb;
use App\Models\Producto;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    public function index()
    {
        $productos = Producto::where('estado', 'activo')->get();
        return view('cliente.layouts.index', compact('productos'));
    }
    public function show($id)
    {
        $producto = Producto::findOrFail($id);
        return view('cliente.layouts.show', compact('producto'));
    }
    public function mostrarDetalleCarrito()
    {
        $factura = Factura::where('id_cliente', Auth::id())
            ->where('estado_factura', 'pendiente')
            ->with('detalles.producto.promociones') // Asegúrate de cargar las promociones
            ->first();

        if ($factura) {
            foreach ($factura->detalles as $detalle) {
                $producto = $detalle->producto;
                $descuento = 0;

                // Verificar si el producto tiene promociones activas
                if ($producto->promociones->count() > 0) {
                    foreach ($producto->promociones as $promocion) {
                        $fechaActual = now()->format('Y-m-d');
                        if ($fechaActual >= $promocion->fecha_inicio && $fechaActual <= $promocion->fecha_fin) {
                            $descuento = $promocion->descuento;
                            break;
                        }
                    }
                }

                // Aplicar el descuento al precio del producto
                $precioConDescuento = $producto->precio - ($producto->precio * ($descuento / 100));
                $detalle->total = $precioConDescuento * $detalle->cantidad;
                $detalle->save();
            }

            // Actualizar el total de la factura con el subtotal y el descuento
            $subtotal = $factura->detalles->sum('total');
            $descuentoTotal = $factura->detalles->sum(function ($detalle) {
                return $detalle->producto->precio * ($detalle->producto->descuento / 100) * $detalle->cantidad;
            });

            $factura->sub_total = $subtotal;
            $factura->descuento = $descuentoTotal;
            $factura->total_factura = $subtotal - $descuentoTotal;
            $factura->save();
        }

        return view('cliente.layouts.index', compact('factura'));
    }


    public function mostrarCat(Request $request, $id_categoria)
    {
        try {
            $categoria = Categoria::findOrFail($id_categoria);
            $productos = Producto::where('id_categoria', $id_categoria)
                ->where('estado', 'activo')
                ->get();
            $categorias = Categoria::all();
            return view('cliente.layouts.categoria', compact('categorias', 'categoria', 'productos'));
        } catch (\Exception $e) {
            return redirect()->route('error.404');
        }
    }



    public function eliminarDelCarrito($id_detalle)
    {
        $detalle = DetalleFactura::findOrFail($id_detalle);
        $factura = $detalle->factura;

        $factura->sub_total -= $detalle->total;
        $factura->total_factura = $factura->sub_total - $factura->descuento;
        $factura->save();

        $detalle->delete();

        return redirect()->route('cliente.carrito.ver');
    }

    public function actualizarCantidad(Request $request, $id_detalle)
    {
        $detalle = DetalleFactura::findOrFail($id_detalle);
        $producto = $detalle->producto;

        $detalle->cantidad = $request->input('cantidad');
        $detalle->total = $detalle->cantidad * ($producto->precio - ($producto->precio * ($producto->descuento / 100)));
        $detalle->save();

        $factura = $detalle->factura;
        $factura->sub_total = $factura->detalles->sum('total');
        $factura->total_factura = $factura->sub_total - $factura->descuento;
        $factura->save();

        return redirect()->route('cliente.carrito.ver');
    }


    public function agregarAlCarrito(Request $request, $id_producto)
    {
        $producto = Producto::findOrFail($id_producto);

        // Verificar si ya existe una factura pendiente para el cliente
        $factura = Factura::where('id_cliente', Auth::id())->where('estado_factura', 'pendiente')->first();

        if (!$factura) {
            // Crear una nueva factura
            $factura = new Factura();
            $factura->id_cliente = Auth::id();
            $factura->fecha = now()->toDateString();
            $factura->hora = now()->toTimeString();
            $factura->sub_total = 0;
            $factura->descuento = 0;
            $factura->total_factura = 0;
            $factura->save();
        }

        // Verificar si el producto ya está en el carrito
        $detalle = DetalleFactura::where('id_factura', $factura->id_factura)
            ->where('id_producto', $producto->id_producto)
            ->first();

        if ($detalle) {
            // Si ya está, actualizar la cantidad
            $detalle->cantidad += 1;
            $detalle->total = $detalle->cantidad * $producto->total_venta;
            $detalle->save();
        } else {
            // Si no está, agregarlo al carrito
            $detalle = new DetalleFactura();
            $detalle->id_factura = $factura->id_factura;
            $detalle->id_producto = $producto->id_producto;
            $detalle->cantidad = 1;
            $detalle->total = $producto->total_venta;
            $detalle->save();
        }

        // Actualizar el total de la factura
        $factura->sub_total += $producto->total_venta;
        $factura->total_factura = $factura->sub_total - $factura->descuento;
        $factura->save();

        return redirect()->route('cliente.carrito.ver');
    }

    public function pedidoConfirmado()
    {
        return view('cliente.layouts.pedidoConfirmado');
    }

    public function realizarPedido(Request $request)
    {
        // Imprimir valores recibidos del request
        // dd($request->all());
        // Buscar la factura pendiente del cliente
        $factura = Factura::where('id_cliente', Auth::id())
            ->where('estado_factura', 'pendiente')
            ->first();

        if (!$factura) {
            return redirect()->route('cliente.carrito.ver')->with('error', 'No hay productos en el carrito.');
        }

        // Obtener los detalles del carrito para asociarlos con la factura
        $detallesCarrito = DetalleFactura::where('id_factura', $factura->id_factura)->get();

        if ($detallesCarrito->isEmpty()) {
            return redirect()->route('cliente.carrito.ver')->with('error', 'No hay productos en el carrito.');
        }

        // Calcular el subtotal y el total de la factura
        foreach ($detallesCarrito as $detalle) {
            $producto = Producto::find($detalle->id_producto);
            if (!$producto) {
                return redirect()->route('cliente.carrito.ver')->with('error', 'Producto no encontrado.');
            }
            $detalle->total = $producto->total_venta * $detalle->cantidad;
            $detalle->save();
        }

        $factura->sub_total = $detallesCarrito->sum('total');
        $factura->total_factura = $factura->sub_total - $factura->descuento;
        $factura->save();

        // Crear el pedido web y asociarlo con la factura
        $pedidoWeb = new PedidoWeb();
        $pedidoWeb->id_factura = $factura->id_factura;
        $pedidoWeb->pedido = $request->input('pedido');
        $pedidoWeb->telefono_referencia = $request->input('telefono_referencia');
        $pedidoWeb->ubicacion = $request->input('ubicacion');
        $pedidoWeb->referencia_ubicacion = $request->input('referencia_ubicacion');
        $pedidoWeb->nota = $request->input('nota');
        $pedidoWeb->tiempo_demora = now()->addHour(); // Ejemplo: definir un tiempo de demora predeterminado
        $pedidoWeb->save();

        // Actualizar el estado de la factura a 'pagada'
        $factura->estado_factura = 'pagada';
        $factura->save();

        // Construir el array de datos para la API externa
        $lcComerceID = "d029fa3a95e174a19934857f535eb9427d967218a36ea014b70ad704bc6c8d1c";
        $lnMoneda = 2;
        $lnTelefono = Auth::user()->phone;
        $lcNombreUsuario = Auth::user()->name;
        $lnCiNit = $factura->nit;
        $lcNroPago = "UAGRM-CC-GRUPO2-2". rand(100000, 999999);
        $lnMontoClienteEmpresa = $factura->total_factura;
        $lcCorreo = Auth::user()->email;
        $lcUrlCallBack = "http://localhost:8000/";
        $lcUrlReturn = "http://localhost:8000/";
        $laPedidoDetalle = $detallesCarrito->map(function ($detalle) {
            return (object) [
                "Serial" => $detalle->producto->id_producto,
                "Producto" => $detalle->producto->nombre,
                "Cantidad" => $detalle->cantidad,
                "Descuento" => $detalle->producto->descuento,
                "Total" => $detalle->total
            ];
        })->toArray();
        $jsonPedidoDetalle = json_encode($laPedidoDetalle);

        $lcUrl = "";
        $loClient = new Client();

        if ($request->tnTipoServicio == 1) {
            $lcUrl = "https://serviciostigomoney.pagofacil.com.bo/api/servicio/generarqrv2";
        } elseif ($request->tnTipoServicio == 2) {
            $lcUrl = "https://serviciostigomoney.pagofacil.com.bo/api/servicio/realizarpagotigomoneyv2";
        }

        $laHeader = ['Accept' => 'application/json'];
        $laBody   = [
            "tcCommerceID"          => $lcComerceID,
            "tnMoneda"              => $lnMoneda,
            "tnTelefono"            => $lnTelefono,
            'tcNombreUsuario'       => $lcNombreUsuario,
            'tnCiNit'               => $lnCiNit,
            'tcNroPago'             => $lcNroPago,
            "tnMontoClienteEmpresa" => $lnMontoClienteEmpresa,
            "tcCorreo"              => $lcCorreo,
            'tcUrlCallBack'         => $lcUrlCallBack,
            "tcUrlReturn"           => $lcUrlReturn,
            'taPedidoDetalle' => $jsonPedidoDetalle
        ];
        // Imprimir valores enviados a la API externa
        // print_r($laBody);

        try {
            $loResponse = $loClient->post($lcUrl, [
                'headers' => $laHeader,
                'json' => $laBody
            ]);

            $laResult = json_decode($loResponse->getBody()->getContents());

            if ($request->tnTipoServicio == 1) {

                $laValues = explode(";", $laResult->values)[1];


                $laQrImage = "data:image/png;base64," . json_decode($laValues)->qrImage;
                echo '<img src="' . $laQrImage . '" alt="Imagen base64">';
            } elseif ($request->tnTipoServicio == 2) {



                $csrfToken = csrf_token();

                echo '<h5 class="text-center mb-4">' . $laResult->message . '</h5>';
                echo '<p class="blue-text">Transacción Generada: </p><p id="tnTransaccion" class="blue-text">' . $laResult->values . '</p><br>';
                echo '<iframe name="QrImage" style="width: 100%; height: 300px;"></iframe>';
                echo '<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>';

                echo '<script>
                        $(document).ready(function() {
                            function hacerSolicitudAjax(numero) {
                                // Agrega el token CSRF al objeto de datos
                                var data = { _token: "' . $csrfToken . '", tnTransaccion: numero };
                                
                                $.ajax({
                                    url: \'/consultar\',
                                    type: \'POST\',
                                    data: data,
                                    success: function(response) {
                                        var iframe = document.getElementsByName(\'QrImage\')[0];
                                        iframe.contentDocument.open();
                                        iframe.contentDocument.write(response.message);
                                        iframe.contentDocument.close();
                                    },
                                    error: function(error) {
                                        console.error(error);
                                    }
                                });
                            }

                            setInterval(function() {
                                hacerSolicitudAjax(' . $laResult->values . ');
                            }, 7000);
                        });
                    </script>';
            }
        } catch (\Throwable $th) {

            return $th->getMessage() . " - " . $th->getLine();
        }

        return view('cliente.layouts.pedidoConfirmado', compact('factura', 'laQrImage'));
    }

    public function listarPedidos()
    {
        $id_cliente = Auth::id();

        $pedidos = Factura::where('id_cliente', $id_cliente)
            ->where('estado_factura', '!=', 'pendiente')
            ->orderByDesc('fecha')
            ->get();

        return view('cliente.layouts.pedidos', compact('pedidos'));
    }

    public function detalleFactura($id_factura)
    {
        $factura = Factura::findOrFail($id_factura);
        return view('cliente.layouts.detalle', compact('factura'));
    }

    public function verDetalle($id_factura)
    {
        $factura = Factura::findOrFail($id_factura);
        return view('cliente.layouts.detalle', compact('factura'));
    }
    private function recalculateTotals($factura)
    {
        $subtotal = $factura->detalles->sum('total');
        $descuentoTotal = $factura->detalles->sum(function ($detalle) {
            return $detalle->producto->precio * ($detalle->producto->descuento / 100) * $detalle->cantidad;
        });
    
        $factura->sub_total = $subtotal;
        $factura->descuento = $descuentoTotal;
        $factura->total_factura = $subtotal - $descuentoTotal;
        $factura->save();
    }
    
    // Función para verificar la firma del callback
    private function verifySignature($data, $signature)
    {
        $secret = env('YOUR_SECRET_KEY');
        $computedSignature = hash_hmac('sha256', json_encode($data), $secret);
        return hash_equals($computedSignature, $signature);
    }
    public function indexc(Request $request)
    {
        $categorias = Categoria::all();
        $carrito = session()->get('carrito', []);
        $productos = Producto::all();


        return view('cliente.layouts.indexc', compact('productos', 'categorias', 'carrito'));
    }
    public function ConsultarEstado(Request $request)
    {
        $lnTransaccion = $request->tnTransaccion;

        $loClientEstado = new Client();

        $lcUrlEstadoTransaccion = "https://serviciostigomoney.pagofacil.com.bo/api/servicio/consultartransaccion";

        $laHeaderEstadoTransaccion = [
            'Accept' => 'application/json'
        ];

        $laBodyEstadoTransaccion = [
            "TransaccionDePago" => $lnTransaccion
        ];

        $loEstadoTransaccion = $loClientEstado->post($lcUrlEstadoTransaccion, [
            'headers' => $laHeaderEstadoTransaccion,
            'json' => $laBodyEstadoTransaccion
        ]);

        $laResultEstadoTransaccion = json_decode($loEstadoTransaccion->getBody()->getContents());

        $texto = '<h5 class="text-center mb-4">Estado Transacción: ' . $laResultEstadoTransaccion->values->messageEstado . '</h5><br>';

        return response()->json(['message' => $texto]);
    }

    public function urlCallback(Request $request)
    {
        $Venta = $request->input("PedidoID");
        $Fecha = $request->input("Fecha");
        $NuevaFecha = date("Y-m-d", strtotime($Fecha));
        $Hora = $request->input("Hora");
        $MetodoPago = $request->input("MetodoPago");
        $Estado = $request->input("Estado");
        $Ingreso = true;

        try {

            $arreglo = ['error' => 0, 'status' => 1, 'message' => "Pago realizado correctamente.", 'values' => true];
        } catch (\Throwable $th) {
            $arreglo = ['error' => 1, 'status' => 1, 'messageSistema' => "[TRY/CATCH] " . $th->getMessage(), 'message' => "No se pudo realizar el pago, por favor intente de nuevo.", 'values' => false];
        }

        return response()->json($arreglo);
    }
}
