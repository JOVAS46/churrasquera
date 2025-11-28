# 🔧 Configurar Credenciales de PagoFácil

## 📝 Paso 1: Editar el archivo `.env`

El archivo `.env` está en:
```
sistema-web/.env
```

## ✏️ Paso 2: Agregar las Credenciales

Abre el archivo `sistema-web/.env` y agrega estas líneas al final:

```env
# Credenciales de PagoFácil
PAGOFACIL_TOKEN_SERVICE=tu_token_service_aqui
PAGOFACIL_TOKEN_SECRET=tu_token_secret_aqui
```

## 🔑 Paso 3: Reemplazar con tus Credenciales Reales

Según los datos de Postman que me compartiste, las credenciales son:

```env
# Credenciales de PagoFácil
PAGOFACIL_TOKEN_SERVICE=51247fae280c20410824977b0781453df59fad5b23bf2a0d14e884482f91e09078dbe5966e0b970ba696ec4caf9aa5661802935f86717c481f1670e63f35d504a62547a9de71bfc76be2c2ae01039ebcb0f74a96f0f1f56542c8b51ef7a2a6da9ea16f23e52ecc4485b69640297a5ec6a701498d2f0e1b4e7f4b7803bf5c2eba
PAGOFACIL_TOKEN_SECRET=0C351C6679844041AA31AF9C
```

## 📂 Ejemplo Completo del `.env`

Tu archivo `.env` debería verse así al final:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_grupo09sa
DB_USERNAME=postgres
DB_PASSWORD=postgres

# ... otras configuraciones ...

# Credenciales de PagoFácil
PAGOFACIL_TOKEN_SERVICE=51247fae280c20410824977b0781453df59fad5b23bf2a0d14e884482f91e09078dbe5966e0b970ba696ec4caf9aa5661802935f86717c481f1670e63f35d504a62547a9de71bfc76be2c2ae01039ebcb0f74a96f0f1f56542c8b51ef7a2a6da9ea16f23e52ecc4485b69640297a5ec6a701498d2f0e1b4e7f4b7803bf5c2eba
PAGOFACIL_TOKEN_SECRET=0C351C6679844041AA31AF9C
```

## 🔄 Paso 4: Limpiar Caché de Configuración

Después de editar el `.env`, ejecuta:

```bash
cd sistema-web
php artisan config:clear
php artisan cache:clear
```

## ⚠️ IMPORTANTE: Limitaciones Actuales

**Aunque agregues las credenciales, la tabla `pago` NO tiene las columnas necesarias para PagoFácil:**

Columnas faltantes:
- `transaction_id` - Para guardar el ID de transacción de PagoFácil
- `qr_code` - Para guardar el código QR
- `qr_image` - Para guardar la imagen del QR
- `payment_number` - Para el número de pago
- `callback_data` - Para guardar respuestas del callback

## 🛠️ Solución Completa (Si quieres usar PagoFácil Real)

### Opción 1: Crear Migración para Agregar Columnas

```bash
cd sistema-web
php artisan make:migration add_pagofacil_fields_to_pago_table --table=pago
```

Edita la migración creada:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pago', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('id_metodo_pago');
            $table->text('qr_code')->nullable()->after('transaction_id');
            $table->text('qr_image')->nullable()->after('qr_code');
            $table->string('payment_number')->nullable()->after('qr_image');
            $table->json('callback_data')->nullable()->after('payment_number');
            $table->string('client_code')->nullable()->after('callback_data');
            $table->integer('currency')->nullable()->after('client_code');
            $table->timestamp('expires_at')->nullable()->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('pago', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_id',
                'qr_code',
                'qr_image',
                'payment_number',
                'callback_data',
                'client_code',
                'currency',
                'expires_at'
            ]);
        });
    }
};
```

Ejecuta la migración:

```bash
php artisan migrate
```

### Opción 2: Solo Usar Endpoints de Prueba

Si NO necesitas PagoFácil real ahora, simplemente usa los endpoints de prueba:

```bash
# Crear pago de prueba
curl http://localhost:8000/api/pagos/test/crear/3

# Completar pago de prueba
curl http://localhost:8000/api/pagos/test/completar/5
```

## 📍 Dónde Están las Credenciales en el Código

Las credenciales se leen en [PagoFacilController.php](sistema-web/app/Http/Controllers/PagoFacilController.php:20-24):

```php
public function __construct()
{
    $this->tcTokenService = env('PAGOFACIL_TOKEN_SERVICE', 'default_value');
    $this->tcTokenSecret = env('PAGOFACIL_TOKEN_SECRET', 'default_value');
}
```

## ✅ Verificar que las Credenciales Funcionan

Prueba el endpoint de login:

```bash
curl -X POST https://masterqr.pagofacil.com.bo/api/services/v2/login \
  -H "tcTokenService: 51247fae280c20410824977b0781453df59fad5b23bf2a0d14e884482f91e09078dbe5966e0b970ba696ec4caf9aa5661802935f86717c481f1670e63f35d504a62547a9de71bfc76be2c2ae01039ebcb0f74a96f0f1f56542c8b51ef7a2a6da9ea16f23e52ecc4485b69640297a5ec6a701498d2f0e1b4e7f4b7803bf5c2eba" \
  -H "tcTokenSecret: 0C351C6679844041AA31AF9C"
```

Si recibes un `access_token`, las credenciales funcionan ✅

---

## 🎯 Resumen Rápido

1. Abre `sistema-web/.env`
2. Agrega al final:
   ```env
   PAGOFACIL_TOKEN_SERVICE=51247fae280c20410824977b0781453df59fad5b23bf2a0d14e884482f91e09078dbe5966e0b970ba696ec4caf9aa5661802935f86717c481f1670e63f35d504a62547a9de71bfc76be2c2ae01039ebcb0f74a96f0f1f56542c8b51ef7a2a6da9ea16f23e52ecc4485b69640297a5ec6a701498d2f0e1b4e7f4b7803bf5c2eba
   PAGOFACIL_TOKEN_SECRET=0C351C6679844041AA31AF9C
   ```
3. Ejecuta: `php artisan config:clear`
4. (Opcional) Crea migración para agregar columnas faltantes
5. Listo! 🎉
