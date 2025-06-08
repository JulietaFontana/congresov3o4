CREATE DATABASE IF NOT EXISTS congreso;
USE congreso;

-- Tabla de usuarios (sin columna rol)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    dni VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL
);

-- Nueva tabla de roles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL UNIQUE -- ej: 'admin', 'ponente', 'evaluador'
);

-- Tabla de unión usuarios-roles (relación muchos a muchos)
CREATE TABLE IF NOT EXISTS usuario_roles (
    id_usuario INT,
    id_rol INT,
    PRIMARY KEY (id_usuario, id_rol),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_rol) REFERENCES roles(id)
);

-- Insertar roles básicos y extendidos
INSERT INTO roles (nombre) VALUES 
    ('admin'), 
    ('ponente'), 
    ('evaluador'), 
    ('asistente'), 
    ('expositor');

-- Insertar usuario admin
INSERT INTO usuarios (username, password, nombre, apellido, dni, email, telefono)
VALUES (
    'admin@admin.com',
    '$2y$10$gzY2MfhsNoCJlYwIKKjMYuKW1S6aJmixtvTF2OtCkeo5X8kp.6hYq', -- admin123
    'Admin',
    'Principal',
    '12345678',
    'admin@admin.com',
    '123456789'
);

-- Asignar múltiples roles al usuario admin (ej: admin)
INSERT INTO usuario_roles (id_usuario, id_rol)
VALUES 
    (1, 1); -- admin

-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_email VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    leida BOOLEAN NOT NULL DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de certificados
CREATE TABLE IF NOT EXISTS certificados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre_evento VARCHAR(255),
    fecha_emision DATE,
    archivo_certificado VARCHAR(255)
);

-- Tabla de asistencias
CREATE TABLE IF NOT EXISTS asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_evento INT DEFAULT 1,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_usuario, id_evento),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Tabla de tokens QR
CREATE TABLE IF NOT EXISTS qr_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(100) UNIQUE NOT NULL,
    id_evento INT NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    valido BOOLEAN DEFAULT 1
);

-- Tabla de asistencias por token QR (opcional)
CREATE TABLE IF NOT EXISTS asistencias_qr (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Crear usuario de base de datos
CREATE USER 'congreso_user'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON congreso.* TO 'congreso_user'@'localhost';
FLUSH PRIVILEGES;

-- Insertar un nuevo usuario (solo rol ponente)
INSERT INTO usuarios (username, password, nombre, apellido, dni, email, telefono)
VALUES (
    'ponente@ejemplo.com',
    '$2y$10$2u5zF4jXaZhnXAoEyNSe.eUz7PlQUaHoU8hQAV2hArsPvC8I4b79y', -- ponente123
    'Laura',
    'Gómez',
    '87654321',
    'ponente@ejemplo.com',
    '987654321'
);

-- Asignar rol 'ponente' (id_rol = 2)
INSERT INTO usuario_roles (id_usuario, id_rol)
VALUES (
    LAST_INSERT_ID(), -- obtiene el id del usuario recién insertado
    2
);
