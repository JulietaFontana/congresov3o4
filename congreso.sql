-- Borrar la base de datos completa (incluye todas las tablas y datos)
DROP DATABASE IF EXISTS congreso;

-- Crear nuevamente la base y su contenido desde cero
CREATE DATABASE congreso;
USE congreso;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    dni VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL
);

-- Tabla de roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL UNIQUE
);

-- Relación muchos a muchos entre usuarios y roles
CREATE TABLE usuario_roles (
    id_usuario INT,
    id_rol INT,
    PRIMARY KEY (id_usuario, id_rol),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_rol) REFERENCES roles(id) ON DELETE CASCADE
);

-- Tabla de notificaciones
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_email VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    leida BOOLEAN NOT NULL DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Lo único que necesitamos para mostrar notificaciones generales (visibles a todos, no personales) 
-- es permitir mensajes sin destinatario específico (usuario_email NULL)
-- entonces podemos modificar la tabla así:

ALTER TABLE notificaciones 
MODIFY COLUMN usuario_email VARCHAR(100) NULL;

-- Con esto, los administradores pueden seguir enviando notificaciones personales (a un email)
-- o generales (dejando usuario_email en NULL al insertarlas)

-- Si querés, también podemos agregar un campo "tipo" para diferenciar entre generales y personales:
ALTER TABLE notificaciones ADD COLUMN tipo ENUM('general', 'personal') DEFAULT 'general';

-- Así podrías filtrar luego en el sistema dependiendo del tipo:
-- SELECT * FROM notificaciones WHERE tipo = 'general'
-- SELECT * FROM notificaciones WHERE usuario_email = 'ejemplo@correo.com' AND tipo = 'personal';

-- Tabla de certificados
CREATE TABLE certificados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre_evento VARCHAR(255),
    fecha_emision DATE,
    archivo_certificado VARCHAR(255)
);

-- Tabla de asistencias
CREATE TABLE asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_evento INT DEFAULT 1,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_usuario, id_evento),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de tokens QR
CREATE TABLE qr_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(100) UNIQUE NOT NULL,
    id_evento INT NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    valido BOOLEAN DEFAULT 1
);

-- Tabla opcional de asistencias por token QR
CREATE TABLE asistencias_qr (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de Ejes Temáticos
CREATE TABLE ejes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL
);

-- Tabla de Ponencias (✅ con evaluación y comentario)
CREATE TABLE ponencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_eje INT NOT NULL,
    archivo VARCHAR(255) NOT NULL,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    fue_evaluada BOOLEAN DEFAULT 0,
    comentario TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_eje) REFERENCES ejes(id) ON DELETE CASCADE
);
-- Tabla para asignar evaluadores a ponencias
CREATE TABLE ponencia_evaluador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ponencia INT NOT NULL,
    id_evaluador INT NOT NULL,
    evaluacion TEXT,
    fecha_evaluacion DATETIME,
    FOREIGN KEY (id_ponencia) REFERENCES ponencias(id) ON DELETE CASCADE,
    FOREIGN KEY (id_evaluador) REFERENCES usuarios(id) ON DELETE CASCADE
);


-- Insertar roles principales
INSERT INTO roles (nombre) VALUES 
    ('admin'), 
    ('ponente'), 
    ('evaluador'), 
    ('asistente'), 
    ('expositor');

-- Insertar usuarios con contraseña "admin123"
INSERT INTO usuarios (username, password, nombre, apellido, dni, email, telefono) VALUES
('admin@admin.com', '$2y$10$HxQTM.aQ5WVPGV1Vrqi2OepJ6BEPE1fuhZjjPk7ogIC5IWFrOBAjC', 'Admin', 'Principal', '12345678', 'admin@admin.com', '123456789'),
('ponente1@correo.com', '$2y$10$HxQTM.aQ5WVPGV1Vrqi2OepJ6BEPE1fuhZjjPk7ogIC5IWFrOBAjC', 'Laura', 'Gómez', '87654321', 'ponente1@correo.com', '111111111'),
('evaluador1@correo.com', '$2y$10$HxQTM.aQ5WVPGV1Vrqi2OepJ6BEPE1fuhZjjPk7ogIC5IWFrOBAjC', 'Carlos', 'Pérez', '11223344', 'evaluador1@correo.com', '222222222'),
('asistente1@correo.com', '$2y$10$HxQTM.aQ5WVPGV1Vrqi2OepJ6BEPE1fuhZjjPk7ogIC5IWFrOBAjC', 'Ana', 'Martínez', '33445566', 'asistente1@correo.com', '333333333'),
('expositor1@correo.com', '$2y$10$HxQTM.aQ5WVPGV1Vrqi2OepJ6BEPE1fuhZjjPk7ogIC5IWFrOBAjC', 'Luis', 'Sosa', '44556677', 'expositor1@correo.com', '444444444');

INSERT INTO ejes (nombre) VALUES 
('Tecnología e Innovación'),
('Educación y Sociedad'),
('Salud y Medio Ambiente');

-- Asignar roles
INSERT INTO usuario_roles (id_usuario, id_rol) VALUES
(1, 1), -- admin
(2, 2), -- ponente
(3, 3), -- evaluador
(4, 4), -- asistente
(5, 5); -- expositor

-- Crear usuario MySQL (opcional)
CREATE USER IF NOT EXISTS 'congreso_user'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON congreso.* TO 'congreso_user'@'localhost';
FLUSH PRIVILEGES;


ALTER TABLE ponencia_evaluador
ADD COLUMN estado ENUM('aprobada', 'desaprobada') DEFAULT NULL;
