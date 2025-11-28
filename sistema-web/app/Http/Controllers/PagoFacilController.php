<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Pedido;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class PagoFacilController extends Controller
{
    private $apiUrl = 'https://masterqr.pagofacil.com.bo/api/services/v2';
    private $tcTokenService;
    private $tcTokenSecret;

    public function __construct()
    {
        $this->tcTokenService = env('PAGOFACIL_TOKEN_SERVICE', '51247fae280c20410824977b0781453df59fad5b23bf2a0d14e884482f91e09078dbe5966e0b970ba696ec4caf9aa5661802935f86717c481f1670e63f35d504a62547a9de71bfc76be2c2ae01039ebcb0f74a96f0f1f56542c8b51ef7a2a6da9ea16f23e52ecc4485b69640297a5ec6a701498d2f0e1b4e7f4b7803bf5c2eba');
        $this->tcTokenSecret = env('PAGOFACIL_TOKEN_SECRET', '0C351C6679844041AA31AF9C');
    }

    /**
     * Obtener token de autenticación de PagoFácil
     */
    private function getAuthToken()
    {
        try {
            Log::info('Intentando autenticar con PagoFácil', [
                'url' => $this->apiUrl . '/login',
                'tcTokenService' => substr($this->tcTokenService, 0, 20) . '...',
                'tcTokenSecret' => $this->tcTokenSecret
            ]);

            $response = Http::withHeaders([
                'tctokenservice' => $this->tcTokenService,
                'tctokensecret' => $this->tcTokenSecret
            ])->post($this->apiUrl . '/login');

            Log::info('Respuesta de PagoFácil Login', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'json' => $response->json()
            ]);

            if ($response->successful() && isset($response->json()['values']['accessToken'])) {
                return $response->json()['values']['accessToken'];
            }

            Log::error('PagoFácil Login Failed', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PagoFácil Login Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Mostrar página de pago para un pedido
     */
    public function mostrarPago($pedidoId)
    {
        $pedido = Pedido::with(['detalles.producto'])
                       ->findOrFail($pedidoId);

        // Verificar que el pedido no tenga un pago completado
        $pagoExistente = Pago::where('id_pedido', $pedidoId)
                           ->where('estado', 'completado')
                           ->first();

        if ($pagoExistente) {
            return redirect()->route('home')->with('success', 'Este pedido ya ha sido pagado.');
        }

        // Hardcodear la URL de pago
        $urlPago = "https://mail.tecnoweb.org.bo/inf513/grupo09sa/churrasquera/sistema-web/public/pago/pedido/$pedidoId";

        // Datos del cliente (puedes obtenerlos de la sesión o request)
        $clientData = [
            'pedidoId' => $pedidoId,
            'clientName' => auth()->user()->name ?? 'Cliente',
            'documentType' => 1, // 1 = CI, 2 = Pasaporte
            'documentId' => '0000000',
            'phoneNumber' => '00000000',
            'email' => auth()->user()->email ?? 'cliente@example.com',
            'urlPago' => $urlPago
        ];

        return view('pagos.qr', $clientData);
    }

    /**
     * Generar código QR para pago
     */
    public function generarQR(Request $request, $pedidoId)
    {
        $request->validate([
            'client_name' => 'required|string|max:100',
            'document_type' => 'required|integer|in:1,2',
            'document_id' => 'required|string|max:20',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|email'
        ]);

        try {
            DB::beginTransaction();

            $pedido = Pedido::with('detalles.producto')->findOrFail($pedidoId);

            // Obtener método de pago QR
            $metodoPagoQR = MetodoPago::firstOrCreate([
                'nombre' => 'QR PagoFácil'
            ], [
                'descripcion' => 'Pago mediante código QR con PagoFácil',
                'activo' => true
            ]);

            // Buscar o crear el pago
            $pago = Pago::firstOrCreate(
                ['id_pedido' => $pedidoId],
                [
                    'id_metodo_pago' => $metodoPagoQR->id_metodo_pago,
                    'monto' => $pedido->total,
                    'estado' => 'pendiente'
                ]
            );

            // Si el pago ya está completado, no permitir regenerar QR
            if ($pago->isCompletado()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido ya ha sido pagado'
                ], 400);
            }

            // Si el pago existe pero está pendiente o cancelado, actualizarlo
            if ($pago->wasRecentlyCreated === false) {
                $pago->update([
                    'estado' => 'pendiente',
                    'monto' => $pedido->total
                ]);
            }

            // Obtener token de autenticación
            $token = $this->getAuthToken();
            if (!$token) {
                throw new \Exception('No se pudo autenticar con PagoFácil');
            }

            // Generar número de pago único (máximo 20 caracteres según documentación)
            $paymentNumber = 'P' . $pago->id_pago . '-' . substr(time(), -8);

            // URL de callback - debe ser HTTPS en producción
            // Para desarrollo local, usa una URL pública temporal o un servicio como ngrok
            $callbackUrl = env('PAGOFACIL_CALLBACK_URL', 'https://tu-dominio.com/pagofacil/callback');

            // Preparar datos para el QR según documentación PagoFácil v2
            $qrData = [
                'paymentMethod' => (int) Pago::METODO_QR,
                'clientName' => trim($request->client_name),
                'documentType' => (int) $request->document_type,
                'documentId' => trim($request->document_id),
                'phoneNumber' => trim($request->phone_number),
                'email' => trim($request->email),
                'paymentNumber' => $paymentNumber,
                'amount' => (float) $pedido->total,
                'currency' => (int) Pago::MONEDA_BOB,
                'clientCode' => '11001', // Código de cliente según documentación
                'callbackUrl' => $callbackUrl,
                'orderDetail' => []
            ];

            // Agregar detalles del pedido
            foreach ($pedido->detalles as $index => $detalle) {
                $qrData['orderDetail'][] = [
                    'serial' => (int) ($index + 1),
                    'product' => (string) $detalle->producto->nombre,
                    'quantity' => (int) $detalle->cantidad,
                    'price' => (float) $detalle->precio_unitario,
                    'discount' => (float) 0,
                    'total' => (float) $detalle->subtotal
                ];
            }

            Log::info('Generando QR de PagoFácil', [
                'url' => $this->apiUrl . '/generate-qr',
                'data' => $qrData
            ]);

            // Generar QR (endpoint correcto es /generate-qr para PagoFácil v2)
            $response = Http::withToken($token)
                          ->post($this->apiUrl . '/generate-qr', $qrData);

            Log::info('Respuesta de PagoFácil Generate QR', [
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                Log::info('✅ QR GENERADO EXITOSAMENTE', [
                    'response_completa' => $responseData
                ]);

                // Extraer datos importantes de la respuesta
                $values = $responseData['values'] ?? [];

                Log::info('📦 VALUES EXTRAÍDOS', [
                    'values' => $values,
                    'tiene_transactionId' => isset($values['transactionId']),
                    'transactionId' => $values['transactionId'] ?? 'NO ENCONTRADO'
                ]);

                // Guardar transactionId en cache
                if (isset($values['transactionId']) && isset($pago->id_pago)) {
                    Cache::put('pagofacil:transaction:' . $pago->id_pago, $values['transactionId'], now()->addMinutes(30));
                    Log::info('💾 Transaction ID guardado en cache', [
                        'pago_id' => $pago->id_pago,
                        'transaction_id' => $values['transactionId']
                    ]);
                }

                // Consultar a la API de PagoFácil inmediatamente para ver si ya está pagado
                $pagado = false;
                if (isset($values['transactionId'])) {
                    $token = $this->getAuthToken();
                    if ($token) {
                        $consulta = \Illuminate\Support\Facades\Http::withToken($token)
                            ->get($this->apiUrl . '/query-transaction', [
                                'transactionId' => $values['transactionId']
                            ]);
                        if ($consulta->successful()) {
                            $transactionData = $consulta->json();
                            if (isset($transactionData['status']) && in_array($transactionData['status'], ['COMPLETED', 'PAID'])) {
                                $pago->marcarComoCompletado($transactionData);
                                $pagado = true;
                            }
                        }
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => '✅ Código QR generado exitosamente',
                    'pago' => $pago->fresh(),
                    'payment_number' => $paymentNumber,
                    'qr' => [
                        'transaction_id' => $values['transactionId'] ?? null,
                        'payment_method_transaction_id' => $values['paymentMethodTransactionId'] ?? null,
                        'qr_base64' => $values['qrBase64'] ?? null,
                        'qr_image_url' => $values['qrBase64'] ? 'data:image/png;base64,' . $values['qrBase64'] : null,
                        'expiration_date' => $values['expirationDate'] ?? null,
                        'status' => $values['status'] ?? null,
                        'checkout_url' => $values['checkoutUrl'] ?? null,
                        'deep_link' => $values['deepLink'] ?? null,
                        'universal_url' => $values['universalUrl'] ?? null,
                    ],
                    'pagado' => $pagado,
                    'instrucciones' => [
                        '1' => 'Muestra el código QR al cliente',
                        '2' => 'El cliente escanea el QR con su app bancaria',
                        '3' => 'Cuando pague, recibirás una notificación en: ' . $callbackUrl,
                        '4' => 'El QR expira en: ' . ($values['expirationDate'] ?? 'No especificado')
                    ]
                ]);

            } else {
                Log::error('PagoFácil QR Generation Failed', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'data' => $qrData
                ]);

                throw new \Exception('Error al generar el código QR: ' . $response->body());
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error generating QR', [
                'error' => $e->getMessage(),
                'pedido_id' => $pedidoId
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Consultar estado de pago
     * Este endpoint se puede llamar desde el frontend para verificar si el pago ya se completó
     */
    public function consultarEstado($pagoId)
    {
        try {
            $pago = Pago::with('pedido')->findOrFail($pagoId);

            // Buscar transactionId en cache
            $transactionId = Cache::get('pagofacil:transaction:' . $pagoId);
            $apiStatus = null;
            $transactionData = null;

            if ($transactionId) {
                $token = $this->getAuthToken();
                if ($token) {
                    $response = \Illuminate\Support\Facades\Http::withToken($token)
                        ->get($this->apiUrl . '/query-transaction', [
                            'transactionId' => $transactionId
                        ]);
                    if ($response->successful()) {
                        $transactionData = $response->json();
                        if (isset($transactionData['status'])) {
                            $apiStatus = $transactionData['status'];
                            // Actualizar estado local según respuesta de la API
                            if (in_array($apiStatus, ['COMPLETED', 'PAID'])) {
                                $pago->marcarComoCompletado($transactionData);
                            } elseif (in_array($apiStatus, ['CANCELLED', 'FAILED'])) {
                                $pago->marcarComoCancelado();
                            }
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'pago' => [
                    'id_pago' => $pago->id_pago,
                    'estado' => $pago->estado,
                    'monto' => $pago->monto,
                    'fecha_pago' => $pago->fecha_pago,
                    'completado' => $pago->isCompletado(),
                    'pendiente' => $pago->isPendiente(),
                    'cancelado' => $pago->isCancelado()
                ],
                'pedido' => $pago->pedido ? [
                    'id_pedido' => $pago->pedido->id_pedido,
                    'estado' => $pago->pedido->estado,
                    'estado_pago' => $pago->pedido->estado_pago ?? null,
                    'total' => $pago->pedido->total
                ] : null,
                'transaction_data' => $transactionData,
                'transaction_id' => $transactionId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pago no encontrado'
            ], 404);
        }

        /* CÓDIGO COMENTADO - Requiere transaction_id que no existe
        try {
            $token = $this->getAuthToken();
            if (!$token) {
                throw new \Exception('No se pudo autenticar con PagoFácil');
            }

            $response = Http::withToken($token)
                          ->get($this->apiUrl . '/query-transaction', [
                              'transactionId' => $pago->transaction_id
                          ]);

            if ($response->successful()) {
                $transactionData = $response->json();
                
                // Actualizar estado según respuesta
                if (isset($transactionData['status'])) {
                    switch ($transactionData['status']) {
                        case 'COMPLETED':
                        case 'PAID':
                            $pago->marcarComoCompletado($transactionData);
                            break;
                        case 'CANCELLED':
                        case 'FAILED':
                            $pago->marcarComoCancelado();
                            break;
                    }
                }

                return response()->json([
                    'success' => true,
                    'pago' => $pago->fresh(),
                    'transaction_data' => $transactionData
                ]);
            }

            throw new \Exception('Error al consultar el estado del pago');

        } catch (\Exception $e) {
            Log::error('Error consulting payment status', [
                'error' => $e->getMessage(),
                'pago_id' => $pagoId
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
        */
    }

    /**
     * Callback de PagoFácil
     * Este endpoint recibe notificaciones automáticas cuando se completa un pago
     */
    public function callback(Request $request)
    {
        Log::info('🔔 PagoFácil Callback Received', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            // PagoFácil envía el transactionId en el callback
            $transactionId = $request->input('transactionId')
                          ?? $request->input('transaction_id')
                          ?? $request->input('TransactionId');

            $paymentNumber = $request->input('paymentNumber')
                          ?? $request->input('payment_number')
                          ?? $request->input('PaymentNumber');

            $status = $request->input('status') ?? $request->input('Status');

            Log::info('Datos extraídos del callback', [
                'transactionId' => $transactionId,
                'paymentNumber' => $paymentNumber,
                'status' => $status
            ]);

            // Buscar el pago por payment_number
            // El payment_number tiene formato: P{id_pago}-{timestamp}
            if ($paymentNumber && preg_match('/^P(\d+)-/', $paymentNumber, $matches)) {
                $pagoId = $matches[1];
                $pago = Pago::with('pedido')->find($pagoId);
            } else {
                // Si no encontramos por payment_number, buscar por transaction_id en los logs
                Log::warning('No se pudo extraer ID del payment_number', ['paymentNumber' => $paymentNumber]);
                return response()->json(['message' => 'Payment number format invalid'], 400);
            }

            if (!$pago) {
                Log::warning('❌ Payment not found in callback', [
                    'pagoId' => $pagoId ?? null,
                    'paymentNumber' => $paymentNumber
                ]);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            // Actualizar estado según el status recibido
            $statusValue = is_numeric($status) ? (int) $status : strtoupper($status);

            // Estados de PagoFácil:
            // 1 = Pendiente, 2 = Completado, 3 = Rechazado, 4 = Anulado
            if ($statusValue === 2 || $statusValue === 'COMPLETED' || $statusValue === 'PAID' || $statusValue === 'SUCCESS') {
                Log::info('✅ Marcando pago como completado', ['pago_id' => $pago->id_pago]);

                // Actualizar pago a completado
                $pago->update([
                    'estado' => 'completado',
                    'fecha_pago' => now()
                ]);

                // Actualizar estado del pedido
                if ($pago->pedido) {
                    $pago->pedido->update([
                        'estado' => 'pagado',
                        'estado_pago' => 'pagado'
                    ]);

                    Log::info('✅ Pedido actualizado a PAGADO', ['pedido_id' => $pago->pedido->id_pedido]);
                } else {
                    Log::warning('⚠️ Pago sin pedido asociado', ['pago_id' => $pago->id_pago]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment completed successfully'
                ]);

            } elseif ($statusValue === 3 || $statusValue === 4 || $statusValue === 'CANCELLED' || $statusValue === 'FAILED') {
                Log::warning('❌ Marcando pago como cancelado', ['pago_id' => $pago->id_pago]);

                $pago->marcarComoCancelado();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment cancelled'
                ]);
            }

            // Estado desconocido
            Log::warning('⚠️ Estado desconocido recibido', ['status' => $statusValue]);

            return response()->json([
                'success' => true,
                'message' => 'Callback received but status not recognized'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Callback processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing callback'
            ], 500);
        }
    }

    /**
     * Cancelar pago pendiente
     */
    public function cancelarPago($pagoId)
    {
        $pago = Pago::findOrFail($pagoId);

        if ($pago->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden cancelar pagos pendientes'
            ]);
        }

        $pago->marcarComoCancelado();

        return response()->json([
            'success' => true,
            'message' => 'Pago cancelado exitosamente'
        ]);
    }

    /**
     * Listar pagos (para administración)
     */
    public function index(Request $request)
    {
        $query = Pago::with(['pedido', 'metodoPago'])
                    ->orderBy('fecha_pago', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_pago', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_pago', '<=', $request->fecha_hasta);
        }

        $pagos = $query->paginate(15);

        // Estadísticas
        $estadisticas = [
            'total_hoy' => Pago::hoy()->sum('monto'),
            'completados_hoy' => Pago::hoy()->completados()->count(),
            'pendientes' => Pago::pendientes()->count(),
            'vencidos' => Pago::vencidos()->count()
        ];

        return Inertia::render('Pagos/Index', [
            'pagos' => $pagos,
            'estadisticas' => $estadisticas,
            'filtros' => $request->all()
        ]);
    }

    /**
     * Ver detalles de un pago
     */
    public function show($pagoId)
    {
        $pago = Pago::with(['pedido.detalles.producto', 'metodoPago'])
                   ->findOrFail($pagoId);

        return Inertia::render('Pagos/Show', [
            'pago' => $pago
        ]);
    }

    /**
     * Marcar pedido como pagado con otros métodos
     */
    public function marcarPagado(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedido,id_pedido',
            'metodo' => 'required|in:efectivo,tarjeta',
            'monto' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $pedido = Pedido::findOrFail($request->pedido_id);

            // Verificar que no tenga un pago completado
            $pagoExistente = Pago::where('id_pedido', $request->pedido_id)
                               ->where('estado', 'completado')
                               ->first();

            if ($pagoExistente) {
                return back()->with('error', 'Este pedido ya ha sido pagado.');
            }

            // Obtener o crear método de pago
            $nombreMetodo = $request->metodo === 'efectivo' ? 'Efectivo' : 'Tarjeta de Débito/Crédito';
            $metodoPago = MetodoPago::firstOrCreate([
                'nombre' => $nombreMetodo
            ], [
                'descripcion' => $nombreMetodo,
                'activo' => true
            ]);

            // Crear pago
            $pago = Pago::create([
                'id_pedido' => $request->pedido_id,
                'id_metodo_pago' => $metodoPago->id_metodo_pago,
                'monto' => $request->monto,
                'estado' => 'completado',
                'fecha_pago' => now()
            ]);

            // Actualizar estado del pedido
            $pedido->update(['estado_pago' => 'pagado']);

            DB::commit();

            return back()->with('success', 'Pago registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error marking payment as paid', [
                'error' => $e->getMessage(),
                'pedido_id' => $request->pedido_id
            ]);

            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    /**
     * 🧪 ENDPOINT DE PRUEBA - Probar autenticación
     */
    public function testAutenticacion()
    {
        try {
            Log::info('=== INICIO TEST AUTENTICACIÓN ===');

            $token = $this->getAuthToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener el token',
                    'instrucciones' => 'Revisa los logs en storage/logs/laravel.log para ver la respuesta completa de la API'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Autenticación exitosa',
                'token' => substr($token, 0, 50) . '...',
                'token_length' => strlen($token)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Servicios habilitados (para debugging)
     */
    public function serviciosHabilitados()
    {
        try {
            $token = $this->getAuthToken();
            if (!$token) {
                throw new \Exception('No se pudo autenticar');
            }

            Log::info('Consultando servicios habilitados', [
                'url' => $this->apiUrl . '/list-enabled-services',
                'token' => substr($token, 0, 30) . '...'
            ]);

            $response = Http::withToken($token)
                          ->post($this->apiUrl . '/list-enabled-services');

            Log::info('Respuesta servicios habilitados', [
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json()
            ]);

            return response()->json([
                'status' => $response->status(),
                'data' => $response->json()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🧪 ENDPOINT DE PRUEBA - Simular callback de PagoFácil
     */
    public function simularCallback($pagoId)
    {
        try {
            $pago = Pago::findOrFail($pagoId);

            // Simular el payload que PagoFácil enviaría
            $paymentNumber = 'P' . $pago->id_pago . '-' . substr(time(), -8);

            $simulatedPayload = [
                'transactionId' => rand(1000000, 9999999),
                'paymentNumber' => $paymentNumber,
                'status' => 2, // 2 = Completado
                'amount' => $pago->monto,
                'currency' => 2
            ];

            Log::info('🧪 Simulando callback de PagoFácil', $simulatedPayload);

            // Llamar al callback con los datos simulados
            $request = new \Illuminate\Http\Request($simulatedPayload);
            return $this->callback($request);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🧪 ENDPOINT DE PRUEBA - Simular pago sin PagoFácil
     * GET /api/pagos/test/completar/{pagoId}
     */
    public function simularPagoCompletado($pagoId)
    {
        try {
            $pago = Pago::with('pedido')->findOrFail($pagoId);

            if ($pago->estado === 'completado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pago ya está completado',
                    'pago' => $pago
                ]);
            }

            // Marcar como completado
            $pago->marcarComoCompletado();

            // Actualizar pedido
            if ($pago->pedido) {
                $pago->pedido->update([
                    'estado' => 'pagado',
                    'estado_pago' => 'pagado'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Pago simulado como completado',
                'pago' => $pago->fresh()->load('pedido')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🧪 ENDPOINT DE PRUEBA - Crear pago de prueba sin API
     * POST /api/pagos/test/crear/{pedidoId}
     */
    public function crearPagoPrueba($pedidoId)
    {
        try {
            DB::beginTransaction();

            $pedido = Pedido::with('detalles.producto')->findOrFail($pedidoId);

            // Cancelar pagos pendientes
            Pago::where('id_pedido', $pedidoId)
                ->where('estado', 'pendiente')
                ->update(['estado' => 'cancelado']);

            // Obtener o crear método de pago
            $metodoPagoQR = MetodoPago::firstOrCreate([
                'nombre' => 'QR PagoFácil'
            ], [
                'descripcion' => 'Pago mediante código QR con PagoFácil',
                'activo' => true
            ]);

            // Crear pago de prueba
            $pago = Pago::create([
                'id_pedido' => $pedidoId,
                'id_metodo_pago' => $metodoPagoQR->id_metodo_pago,
                'monto' => $pedido->total,
                'estado' => 'pendiente'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Pago de prueba creado',
                'pago' => $pago,
                'instrucciones' => [
                    'paso1' => 'Pago creado con estado PENDIENTE',
                    'paso2' => 'Para completar el pago, usa:',
                    'endpoint' => url("/api/pagos/test/completar/{$pago->id_pago}"),
                    'metodo' => 'GET'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
