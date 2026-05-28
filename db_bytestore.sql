-- =====================================================
-- ByteStore - Database Schema
-- =====================================================

CREATE DATABASE IF NOT EXISTS db_bytestore 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE db_bytestore;

-- =====================================================
-- TABLA: USUARIO
-- =====================================================
CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: CATEGORIA
-- =====================================================
CREATE TABLE IF NOT EXISTS categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: PRODUCTOS
-- =====================================================
CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre_producto VARCHAR(255) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    imagen_url VARCHAR(255),
    id_categoria INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_categoria 
        FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: PEDIDO
-- =====================================================
CREATE TABLE IF NOT EXISTS pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_pedido DATE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedido_usuario 
        FOREIGN KEY (id_usuario) REFERENCES usuario(id) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: DETALLE_PEDIDO
-- =====================================================
CREATE TABLE IF NOT EXISTS detalle_pedido (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_detalle_pedido 
        FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detalle_producto 
        FOREIGN KEY (id_producto) REFERENCES productos(id_producto) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS DE EJEMPLO
-- =====================================================

INSERT INTO categoria (nombre_categoria) VALUES 
('RPG'),
('Acción'),
('Deportes'),
('Aventura');

INSERT INTO productos (nombre_producto, precio, id_categoria, descripcion) VALUES 
('Elden Ring', 59.99, 1, 'Un ARPG épico'),
('Street Fighter 6', 49.99, 2, 'Juego de lucha legendario'),
('EA Sports FC 26', 59.99, 3, 'Simulador de fútbol'),
('The Legend of Zelda', 69.99, 4, 'Aventura clásica de Nintendo'),
('Minecraft', 29.99, 4, 'Sandbox creativo'),
('Resident Evil 4 Remake', 49.99, 2, 'Terror y acción');

INSERT INTO usuario (nombre, email, password) VALUES 
('Juan Admin', 'admin@bytestore.com', '$2y$10$M9LFfRDHdK5fM7eP9gW8R.8kL4xQ2zJ1hM5cN3oK6pR8vS9tU');
