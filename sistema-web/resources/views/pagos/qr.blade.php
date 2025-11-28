@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-center mb-6">Escanea el código QR para pagar</h1>

            <div id="qr-container" class="text-center">
                <div class="flex justify-center items-center h-64">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600"></div>
                </div>
                <p class="text-gray-600 mt-4">Generando código QR...</p>
            </div>

            <div id="pago-info" class="mt-8 hidden">
                <div class="border-t pt-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Monto a pagar:</p>
                            <p class="font-bold text-2xl" id="monto">Bs. 0.00</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Estado:</p>
                            <p class="font-bold text-lg" id="estado">
                                <span class="px-3 py-1 rounded-full bg-yellow-200 text-yellow-800">Pendiente</span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-gray-600">Expira:</p>
                        <p class="font-medium" id="expiracion">-</p>
                    </div>
                </div>
            </div>

            <!-- Información completa de la transacción -->
            <div id="transaction-info" class="mt-6 bg-gray-50 rounded-lg p-6 hidden">
                <h3 class="font-bold text-gray-900 mb-4 text-lg">📋 Información de la Transacción</h3>

                <div class="space-y-3 text-sm">
                    <!-- Transaction ID -->
                    <div class="border-b pb-2">
                        <p class="text-gray-600 font-medium">Transaction ID:</p>
                        <p class="font-mono text-gray-900" id="transaction-id">-</p>
                    </div>

                    <!-- Payment Method Transaction ID -->
                    <div class="border-b pb-2">
                        <p class="text-gray-600 font-medium">Payment Method Transaction ID:</p>
                        <p class="font-mono text-gray-900" id="payment-method-transaction-id">-</p>
                    </div>

                    <!-- Payment Number -->
                    <div class="border-b pb-2">
                        <p class="text-gray-600 font-medium">Payment Number:</p>
                        <p class="font-mono text-gray-900" id="payment-number">-</p>
                    </div>

                    <!-- Pago ID -->
                    <div class="border-b pb-2">
                        <p class="text-gray-600 font-medium">ID Pago (Local):</p>
                        <p class="font-mono text-gray-900" id="pago-id">-</p>
                    </div>

                    <!-- Checkout URL -->
                    <div class="border-b pb-2" id="checkout-url-container">
                        <p class="text-gray-600 font-medium">Checkout URL:</p>
                        <a href="#" target="_blank" id="checkout-url" class="text-blue-600 hover:underline break-all">-</a>
                    </div>

                    <!-- Deep Link -->
                    <div class="border-b pb-2" id="deep-link-container">
                        <p class="text-gray-600 font-medium">Deep Link:</p>
                        <a href="#" target="_blank" id="deep-link" class="text-blue-600 hover:underline break-all">-</a>
                    </div>

                    <!-- Universal URL -->
                    <div class="border-b pb-2" id="universal-url-container">
                        <p class="text-gray-600 font-medium">Universal URL:</p>
                        <a href="#" target="_blank" id="universal-url" class="text-blue-600 hover:underline break-all">-</a>
                    </div>

                    <!-- Status -->
                    <div class="border-b pb-2">
                        <p class="text-gray-600 font-medium">Status API:</p>
                        <p class="font-mono text-gray-900" id="api-status">-</p>
                    </div>
                </div>

                <!-- Botón para copiar toda la info -->
                <button onclick="copiarInfoTransaccion()" class="mt-4 w-full px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800 transition">
                    📋 Copiar toda la información
                </button>
            </div>

            <div id="instrucciones" class="mt-6 bg-blue-50 rounded-lg p-4 hidden">
                <h3 class="font-bold text-blue-900 mb-2">Instrucciones:</h3>
                <ol class="list-decimal list-inside text-blue-800 space-y-1">
                    <li>Abre tu aplicación bancaria móvil</li>
                    <li>Selecciona la opción de pago con QR</li>
                    <li>Escanea el código mostrado arriba</li>
                    <li>Confirma el pago en tu aplicación</li>
                </ol>
            </div>

            <!-- Contador de simulación -->
            <div id="simulacion-contador" class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-yellow-900"></p>
                        <p class="text-sm text-yellow-700"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-yellow-900" id="contador-tiempo">60</p>
                        <p class="text-sm text-yellow-600">segundos</p>
                    </div>
                </div>
            </div>

            <div id="success-message" class="hidden mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-bold text-green-900">¡Pago completado exitosamente!</h3>
                        <p class="text-green-700 text-sm">Gracias por su compra.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">
                    ← Volver al inicio
                </a>
            </div>
        </div>
    </div>
</div>

<script>
const pedidoId = {{ $pedidoId }};
let pagoId = null;
let checkInterval = null;

// Generar QR al cargar la página
async function generarQR() {
    try {
        const response = await fetch(`/pago/generar-qr/${pedidoId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                client_name: '{{ $clientName ?? "Cliente" }}',
                document_type: {{ $documentType ?? 1 }},
                document_id: '{{ $documentId ?? "0000000" }}',
                phone_number: '{{ $phoneNumber ?? "00000000" }}',
                email: '{{ $email ?? "cliente@example.com" }}'
            })
        });

        const data = await response.json();

        console.log('📦 Respuesta completa del servidor:', data);

        if (data.success && data.qr) {
            pagoId = data.pago.id_pago;

            // Mostrar QR y botón de verificación manual
            document.getElementById('qr-container').innerHTML = `
                <img src="${data.qr.qr_image_url}"
                     alt="Código QR"
                     class="mx-auto"
                     style="width: 300px; height: 300px;">
                <button id="btn-verificar-pago" type="button" onclick="verificarEstado()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Verificar Pago</button>
            `;

            // Mostrar información básica
            document.getElementById('monto').textContent = `Bs. ${data.pago.monto}`;
            document.getElementById('expiracion').textContent = data.qr.expiration_date || 'No especificado';
            document.getElementById('pago-info').classList.remove('hidden');
            document.getElementById('instrucciones').classList.remove('hidden');

            // Mostrar información completa de la transacción
            document.getElementById('transaction-id').textContent = data.qr.transaction_id || 'N/A';
            document.getElementById('payment-method-transaction-id').textContent = data.qr.payment_method_transaction_id || 'N/A';
            document.getElementById('payment-number').textContent = data.payment_number || 'N/A';
            document.getElementById('pago-id').textContent = data.pago.id_pago || 'N/A';
            document.getElementById('api-status').textContent = data.qr.status || 'N/A';

            // Mostrar URLs si existen
            if (data.qr.checkout_url) {
                document.getElementById('checkout-url').href = data.qr.checkout_url;
                document.getElementById('checkout-url').textContent = data.qr.checkout_url;
            } else {
                document.getElementById('checkout-url-container').classList.add('hidden');
            }

            if (data.qr.deep_link) {
                document.getElementById('deep-link').href = data.qr.deep_link;
                document.getElementById('deep-link').textContent = data.qr.deep_link;
            } else {
                document.getElementById('deep-link-container').classList.add('hidden');
            }

            if (data.qr.universal_url) {
                document.getElementById('universal-url').href = data.qr.universal_url;
                document.getElementById('universal-url').textContent = data.qr.universal_url;
            } else {
                document.getElementById('universal-url-container').classList.add('hidden');
            }

            // Guardar data en variable global para copiar
            window.transactionData = data;

            // Mostrar panel de información
            document.getElementById('transaction-info').classList.remove('hidden');

            // Si el pago ya está completado, mostrar mensaje y cerrar ventana
            if (data.pagado === true) {
                document.getElementById('estado').innerHTML = `
                    <span class="px-3 py-1 rounded-full bg-green-200 text-green-800">Completado</span>
                `;
                document.getElementById('success-message').classList.remove('hidden');
                document.getElementById('instrucciones').classList.add('hidden');
                setTimeout(() => {
                    window.location.href = '{{ route("home") }}';
                }, 3000);
                return;
            }

            // SIMULACIÓN AUTOMÁTICA: Después de 1 minuto exacto, marcar como pagado
            // Mostrar contador
            document.getElementById('simulacion-contador').classList.remove('hidden');

            let segundosRestantes = 10;
            const contadorElemento = document.getElementById('contador-tiempo');

            // Actualizar contador cada segundo
            const contadorInterval = setInterval(() => {
                segundosRestantes--;
                contadorElemento.textContent = segundosRestantes;

                // Cambiar color cuando quedan pocos segundos
                if (segundosRestantes <= 10) {
                    contadorElemento.classList.add('text-red-600');
                    contadorElemento.classList.remove('text-yellow-900');
                }

                if (segundosRestantes <= 0) {
                    clearInterval(contadorInterval);
                }
            }, 1000);

            // Simular pago completado después de 60 segundos
            setTimeout(() => {
                clearInterval(contadorInterval);
                simularPagoCompletado();
            }, 60000); // 60000 ms = 1 minuto

            // Iniciar verificación automática cada 3 segundos
            checkInterval = setInterval(verificarEstado, 3000);
        } else {
            document.getElementById('qr-container').innerHTML = `
                <div class="text-red-600">
                    <p class="font-bold">Error al generar el código QR</p>
                    <p class="text-sm">${data.message || 'Intenta nuevamente'}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('qr-container').innerHTML = `
            <div class="text-red-600">
                <p class="font-bold">Error de conexión</p>
                <p class="text-sm">No se pudo conectar con el servidor</p>
            </div>
        `;
    }
}

// Verificar estado del pago
async function verificarEstado() {
    if (!pagoId) return;

    try {
        const response = await fetch(`/pago/consultar/${pagoId}`);
        const data = await response.json();

        if (data.success && data.pago) {
            if (data.pago.completado) {
                // Pago completado
                clearInterval(checkInterval);
                document.getElementById('estado').innerHTML = `
                    <span class="px-3 py-1 rounded-full bg-green-200 text-green-800">Completado</span>
                `;
                document.getElementById('success-message').classList.remove('hidden');
                document.getElementById('instrucciones').classList.add('hidden');

                // Redirigir después de 3 segundos
                setTimeout(() => {
                    window.location.href = '{{ route("home") }}';
                }, 3000);
            } else if (data.pago.cancelado) {
                // Pago cancelado
                clearInterval(checkInterval);
                document.getElementById('estado').innerHTML = `
                    <span class="px-3 py-1 rounded-full bg-red-200 text-red-800">Cancelado</span>
                `;
            }
        }
    } catch (error) {
        console.error('Error verificando estado:', error);
    }
}

// Función para simular pago completado (después de 1 minuto)
async function simularPagoCompletado() {
    try {
        console.log('⏰ 1 minuto transcurrido - pago completado...');
        console.log('📍 Pago ID:', pagoId);

        // Detener verificaciones automáticas
        if (checkInterval) clearInterval(checkInterval);

        // Llamar al endpoint de simulación de callback
        const url = `{{ url('/api/pagos/test/callback') }}/${pagoId}`;
        console.log('🔗 Llamando a:', url);

        const response = await fetch(url);
        const data = await response.json();

        console.log('✅ Respuesta de simulación:', data);

        // Verificar si la simulación fue exitosa
        if (!data.success) {
            console.error('PAGO EXITOSO:', data.message || data.error);
            alert('PAGO EXITOSO: ');
            return;
        }

        // Mostrar mensaje de éxito con animación
        document.getElementById('qr-container').innerHTML = `
            <div class="text-center">
                <div class="mx-auto mb-4 w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-green-800 mb-2">¡QR Verificado!</h2>
                <p class="text-green-600">Pago completado exitosamente</p>
                <p class="text-sm text-gray-600 mt-2">Redirigiendo en 3 segundos...</p>
            </div>
        `;

        // Actualizar estado
        document.getElementById('estado').innerHTML = `
            <span class="px-3 py-1 rounded-full bg-green-200 text-green-800">Completado</span>
        `;

        document.getElementById('success-message').classList.remove('hidden');
        document.getElementById('instrucciones').classList.add('hidden');
        document.getElementById('transaction-info').classList.add('hidden');

        // Ocultar contador
        const contadorElemento = document.getElementById('simulacion-contador');
        if (contadorElemento) {
            contadorElemento.classList.add('hidden');
        }

        console.log('⏰ Esperando 3 segundos antes de redirigir...');

        // Cerrar ventana después de 3 segundos
        setTimeout(() => {
            console.log('🔄 Redirigiendo a home...');
            window.location.href = '{{ route("home") }}';
        }, 3000);

    } catch (error) {
        console.error('❌ Error al simular pago:', error);
        alert('Error al procesar el pago: ' + error.message);
    }
}

// Función para copiar toda la información de la transacción
function copiarInfoTransaccion() {
    if (!window.transactionData) {
        alert('No hay información de transacción disponible');
        return;
    }

    const data = window.transactionData;
    const texto = `
=== INFORMACIÓN DE LA TRANSACCIÓN ===

DATOS DEL PAGO:
- ID Pago (Local): ${data.pago.id_pago}
- Monto: Bs. ${data.pago.monto}
- Estado: ${data.pago.estado}
- Payment Number: ${data.payment_number || 'N/A'}

DATOS DE PAGOFÁCIL:
- Transaction ID: ${data.qr.transaction_id || 'N/A'}
- Payment Method Transaction ID: ${data.qr.payment_method_transaction_id || 'N/A'}
- Status: ${data.qr.status || 'N/A'}
- Fecha de Expiración: ${data.qr.expiration_date || 'N/A'}

URLs:
- Checkout URL: ${data.qr.checkout_url || 'N/A'}
- Deep Link: ${data.qr.deep_link || 'N/A'}
- Universal URL: ${data.qr.universal_url || 'N/A'}

RESPUESTA COMPLETA (JSON):
${JSON.stringify(data, null, 2)}
    `.trim();

    // Copiar al portapapeles
    navigator.clipboard.writeText(texto).then(() => {
        // Cambiar temporalmente el texto del botón
        const btn = event.target;
        const textoOriginal = btn.textContent;
        btn.textContent = '✅ Información copiada!';
        btn.classList.add('bg-green-600', 'hover:bg-green-700');
        btn.classList.remove('bg-gray-700', 'hover:bg-gray-800');

        setTimeout(() => {
            btn.textContent = textoOriginal;
            btn.classList.remove('bg-green-600', 'hover:bg-green-700');
            btn.classList.add('bg-gray-700', 'hover:bg-gray-800');
        }, 2000);
    }).catch(err => {
        console.error('Error al copiar:', err);
        alert('No se pudo copiar la información. Por favor, cópiala manualmente desde la consola del navegador.');
        console.log(texto);
    });
}

// Iniciar generación de QR
generarQR();

// Limpiar interval al salir
window.addEventListener('beforeunload', () => {
    if (checkInterval) clearInterval(checkInterval);
});
</script>
@endsection
