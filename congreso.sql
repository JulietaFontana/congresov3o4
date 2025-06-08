CREATE DATABASE IF NOT EXISTS congreso;
USE congreso;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'ponente', 'user') NOT NULL DEFAULT 'user',
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    dni VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL
);

-- Crear usuario admin inicial (email: admin@admin.com, pass: admin123)
INSERT INTO usuarios (username, password, rol, nombre, apellido, dni, email, telefono)
VALUES (
    'admin@admin.com',
    '$2y$10$gzY2MfhsNoCJlYwIKKjMYuKW1S6aJmixtvTF2OtCkeo5X8kp.6hYq', -- hash para admin123
    'admin',
    'Admin',
    'Principal',
    '12345678',
    'admin@admin.com',
    '123456789'
);
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_email VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    leida BOOLEAN NOT NULL DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE certificados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre_evento VARCHAR(255),
    fecha_emision DATE,
    archivo_certificado VARCHAR(255)
);

    --asistencias

    CREATE TABLE IF NOT EXISTS asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_evento INT DEFAULT 1,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_usuario, id_evento),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

 -- qr
 CREATE TABLE qr_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  token VARCHAR(100) UNIQUE NOT NULL,
  id_evento INT NOT NULL DEFAULT 1,
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  valido BOOLEAN DEFAULT 1
);
CREATE TABLE IF NOT EXISTS asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);



    -- Crear usuario con contraseña
    CREATE USER 'congreso_user'@'localhost' IDENTIFIED BY 'password123';

    -- Otorgar permisos para la base de datos congreso
    GRANT ALL PRIVILEGES ON congreso.* TO 'congreso_user'@'localhost';


    -- Aplicar los cambios de permisos
    FLUSH PRIVILEGES;

