-- =====================================================
-- BASE DE DATOS COMPLETA: CHURRASCUTERIA ROBERTO
-- Sistema de Gestión de Restaurante
-- =====================================================

-- Tabla: Rol
CREATE TABLE rol (
    id_rol SERIAL PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: Usuario
CREATE TABLE usuario (
    id_usuario SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado BOOLEAN DEFAULT TRUE,
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES rol(id_rol) ON DELETE RESTRICT
);

-- Tabla: MenuNavegacion (MENÚ DINÁMICO)
CREATE TABLE menu_navegacion (
    id_menu SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    icono VARCHAR(50),
    url VARCHAR(255),
    orden INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    id_padre INT NULL,
    mostrar_en VARCHAR(50) DEFAULT 'ambos' CHECK (mostrar_en IN ('ambos', 'header', 'sidebar')),
    id_rol INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_padre) REFERENCES menu_navegacion(id_menu) ON DELETE CASCADE,
    FOREIGN KEY (id_rol) REFERENCES rol(id_rol) ON DELETE SET NULL
);

-- Índices para MenuNavegacion
CREATE INDEX idx_menu_padre ON menu_navegacion(id_padre);
CREATE INDEX idx_menu_rol ON menu_navegacion(id_rol);
CREATE INDEX idx_menu_orden ON menu_navegacion(orden);

-- Tabla: Categoria
CREATE TABLE categoria (
    id_categoria SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    tipo VARCHAR(20) CHECK (tipo IN ('plato', 'bebida')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: Producto
CREATE TABLE producto (
    id_producto SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL CHECK (precio >= 0),
    tiempo_preparacion INT,
    disponible BOOLEAN DEFAULT TRUE,
    imagen VARCHAR(255),
    id_categoria INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE RESTRICT
);

-- Tabla: Mesa
CREATE TABLE mesa (
    id_mesa SERIAL PRIMARY KEY,
    numero_mesa INT NOT NULL UNIQUE,
    capacidad INT NOT NULL CHECK (capacidad > 0),
    ubicacion VARCHAR(50),
    estado VARCHAR(20) DEFAULT 'disponible' CHECK (estado IN ('disponible', 'ocupada', 'reservada', 'mantenimiento')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: Reserva
CREATE TABLE reserva (
    id_reserva SERIAL PRIMARY KEY,
    fecha_reserva DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    numero_personas INT NOT NULL CHECK (numero_personas > 0),
    estado VARCHAR(20) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'confirmada', 'cancelada', 'completada')),
    observaciones TEXT,
    id_cliente INT NOT NULL,
    id_mesa INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_mesa) REFERENCES mesa(id_mesa) ON DELETE RESTRICT
);

-- Tabla: Pedido
CREATE TABLE pedido (
    id_pedido SERIAL PRIMARY KEY,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(20) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado')),
    total DECIMAL(10,2) DEFAULT 0 CHECK (total >= 0),
    observaciones TEXT,
    id_cliente INT,
    id_mesero INT NOT NULL,
    id_mesa INT NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES usuario(id_usuario) ON DELETE SET NULL,
    FOREIGN KEY (id_mesero) REFERENCES usuario(id_usuario) ON DELETE RESTRICT,
    FOREIGN KEY (id_mesa) REFERENCES mesa(id_mesa) ON DELETE RESTRICT
);

-- Tabla: DetallePedido
CREATE TABLE detalle_pedido (
    id_detalle SERIAL PRIMARY KEY,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    subtotal DECIMAL(10,2) NOT NULL CHECK (subtotal >= 0),
    observaciones TEXT,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE RESTRICT
);

-- Tabla: Proveedor
CREATE TABLE proveedor (
    id_proveedor SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    ruc VARCHAR(20) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: Insumo
CREATE TABLE insumo (
    id_insumo SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    unidad_medida VARCHAR(20) NOT NULL,
    stock_actual DECIMAL(10,2) DEFAULT 0 CHECK (stock_actual >= 0),
    stock_minimo DECIMAL(10,2) NOT NULL CHECK (stock_minimo >= 0),
    precio_unitario DECIMAL(10,2) CHECK (precio_unitario >= 0),
    id_proveedor INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor) ON DELETE SET NULL
);

-- Tabla: Ticket
CREATE TABLE ticket (
    id_ticket SERIAL PRIMARY KEY,
    numero_ticket VARCHAR(20) NOT NULL UNIQUE,
    fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo VARCHAR(20) CHECK (tipo IN ('cocina', 'cliente')),
    estado VARCHAR(20) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'impreso', 'anulado')),
    id_pedido INT NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE
);

-- Tabla: MetodoPago
CREATE TABLE metodo_pago (
    id_metodo_pago SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: Pago
CREATE TABLE pago (
    id_pago SERIAL PRIMARY KEY,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    monto DECIMAL(10,2) NOT NULL CHECK (monto >= 0),
    estado VARCHAR(20) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'completado', 'cancelado')),
    id_pedido INT NOT NULL UNIQUE,
    id_metodo_pago INT NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_metodo_pago) REFERENCES metodo_pago(id_metodo_pago) ON DELETE RESTRICT
);

-- Tabla: Factura
CREATE TABLE factura (
    id_factura SERIAL PRIMARY KEY,
    numero_factura VARCHAR(20) NOT NULL UNIQUE,
    fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL CHECK (subtotal >= 0),
    igv DECIMAL(10,2) DEFAULT 0 CHECK (igv >= 0),
    total DECIMAL(10,2) NOT NULL CHECK (total >= 0),
    id_pago INT NOT NULL UNIQUE,
    FOREIGN KEY (id_pago) REFERENCES pago(id_pago) ON DELETE CASCADE
);

-- Tabla: Bitacora
CREATE TABLE bitacora (
    id_bitacora SERIAL PRIMARY KEY,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50),
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    detalles TEXT,
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL
);

-- Tabla: Caja
CREATE TABLE caja (
    id_caja SERIAL PRIMARY KEY,
    fecha_apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP,
    monto_inicial DECIMAL(10,2) DEFAULT 0 CHECK (monto_inicial >= 0),
    monto_final DECIMAL(10,2) CHECK (monto_final >= 0),
    total_ingresos DECIMAL(10,2) DEFAULT 0 CHECK (total_ingresos >= 0),
    total_egresos DECIMAL(10,2) DEFAULT 0 CHECK (total_egresos >= 0),
    estado VARCHAR(20) DEFAULT 'abierta' CHECK (estado IN ('abierta', 'cerrada')),
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE RESTRICT
);

-- Tabla intermedia: ProductoInsumo (relación muchos a muchos)
CREATE TABLE producto_insumo (
    id_producto_insumo SERIAL PRIMARY KEY,
    cantidad_necesaria DECIMAL(10,2) NOT NULL CHECK (cantidad_necesaria > 0),
    id_producto INT NOT NULL,
    id_insumo INT NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE,
    FOREIGN KEY (id_insumo) REFERENCES insumo(id_insumo) ON DELETE CASCADE,
    UNIQUE(id_producto, id_insumo)
);

-- Tabla: MovimientoInsumo
CREATE TABLE movimiento_insumo (
    id_movimiento SERIAL PRIMARY KEY,
    tipo_movimiento VARCHAR(20) CHECK (tipo_movimiento IN ('entrada', 'salida')),
    cantidad DECIMAL(10,2) NOT NULL CHECK (cantidad > 0),
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT,
    id_insumo INT NOT NULL,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_insumo) REFERENCES insumo(id_insumo) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE RESTRICT
);

-- =====================================================
-- DATOS INICIALES (SEEDERS)
-- =====================================================

-- Insertar Roles
INSERT INTO rol (id_rol, nombre_rol, descripcion, created_at) VALUES
(1, 'Gerente', 'Administrador del restaurante con acceso completo al sistema', NOW()),
(2, 'Cajero', 'Encargado de gestión de pagos, facturas y caja', NOW()),
(3, 'Mesero', 'Encargado de atención al cliente, mesas, reservas y pedidos', NOW()),
(4, 'Cocinero', 'Encargado de preparación de alimentos y gestión de inventario de cocina', NOW()),
(5, 'Cliente', 'Cliente del restaurante con acceso limitado', NOW());

-- Insertar Menús de Navegación
INSERT INTO menu_navegacion (id_menu, nombre, icono, url, orden, activo, id_padre, mostrar_en, id_rol, created_at, updated_at) VALUES
-- Dashboard
(1, 'Dashboard', 'fas fa-home', '/home', 1, true, NULL, 'sidebar', NULL, NOW(), NOW()),

-- Gestión de Mesas
(2, 'Gestión de Mesas', 'fas fa-chair', NULL, 2, true, NULL, 'sidebar', NULL, NOW(), NOW()),
(3, 'Mesas', 'fas fa-table', '/mesas', 1, true, 2, 'sidebar', NULL, NOW(), NOW()),
(4, 'Reservas', 'fas fa-calendar-check', '/reservas', 2, true, 2, 'sidebar', NULL, NOW(), NOW()),

-- Pedidos
(5, 'Pedidos', 'fas fa-shopping-cart', NULL, 3, true, NULL, 'sidebar', NULL, NOW(), NOW()),
(6, 'Nuevo Pedido', 'fas fa-plus-circle', '/pedidos/create', 1, true, 5, 'sidebar', 3, NOW(), NOW()),
(7, 'Lista de Pedidos', 'fas fa-list', '/pedidos', 2, true, 5, 'sidebar', NULL, NOW(), NOW()),

-- Productos
(8, 'Productos', 'fas fa-utensils', NULL, 4, true, NULL, 'sidebar', 1, NOW(), NOW()),
(9, 'Lista de Productos', 'fas fa-hamburger', '/productos', 1, true, 8, 'sidebar', 1, NOW(), NOW()),
(10, 'Categorías', 'fas fa-tags', '/categorias', 2, true, 8, 'sidebar', 1, NOW(), NOW()),

-- Inventario
(11, 'Inventario', 'fas fa-boxes', NULL, 5, true, NULL, 'sidebar', NULL, NOW(), NOW()),
(12, 'Insumos', 'fas fa-box', '/insumos', 1, true, 11, 'sidebar', NULL, NOW(), NOW()),
(13, 'Movimientos', 'fas fa-exchange-alt', '/movimientos', 2, true, 11, 'sidebar', NULL, NOW(), NOW()),
(14, 'Proveedores', 'fas fa-truck', '/proveedores', 3, true, 11, 'sidebar', 1, NOW(), NOW()),

-- Caja
(15, 'Caja', 'fas fa-cash-register', NULL, 6, true, NULL, 'sidebar', NULL, NOW(), NOW()),
(16, 'Pagos', 'fas fa-money-bill-wave', '/pagos', 1, true, 15, 'sidebar', NULL, NOW(), NOW()),
(17, 'Facturas', 'fas fa-file-invoice', '/facturas', 2, true, 15, 'sidebar', NULL, NOW(), NOW()),
(18, 'Gestión de Caja', 'fas fa-cash-register', '/cajas', 3, true, 15, 'sidebar', 2, NOW(), NOW()),

-- Reportes
(19, 'Reportes', 'fas fa-chart-bar', NULL, 7, true, NULL, 'sidebar', 1, NOW(), NOW()),
(20, 'Ventas', 'fas fa-chart-line', '/reportes/ventas', 1, true, 19, 'sidebar', 1, NOW(), NOW()),
(21, 'Inventario', 'fas fa-warehouse', '/reportes/inventario', 2, true, 19, 'sidebar', 1, NOW(), NOW()),
(22, 'Bitácora', 'fas fa-history', '/reportes/bitacora', 3, true, 19, 'sidebar', 1, NOW(), NOW()),

-- Configuración
(23, 'Configuración', 'fas fa-cog', NULL, 8, true, NULL, 'sidebar', 1, NOW(), NOW()),
(24, 'Usuarios', 'fas fa-users', '/users', 1, true, 23, 'sidebar', 1, NOW(), NOW()),
(25, 'Roles', 'fas fa-user-tag', '/roles', 2, true, 23, 'sidebar', 1, NOW(), NOW()),
(26, 'Menú de Navegación', 'fas fa-bars', '/menus', 3, true, 23, 'sidebar', 1, NOW(), NOW());

-- Insertar Métodos de Pago
INSERT INTO metodo_pago (nombre, descripcion, activo, created_at) VALUES
('Efectivo', 'Pago en efectivo', true, NOW()),
('Tarjeta de Débito', 'Pago con tarjeta de débito', true, NOW()),
('Tarjeta de Crédito', 'Pago con tarjeta de crédito', true, NOW()),
('Transferencia Bancaria', 'Pago por transferencia bancaria', true, NOW()),
('QR/Billetera Digital', 'Pago mediante código QR o billetera digital', true, NOW());

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
