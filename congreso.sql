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

ALTER TABLE notificaciones ADD COLUMN tipo ENUM('general', 'personal') DEFAULT 'general';


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

























































-- base de datos funcional sin datos,--
-- Adminer 5.0.5 MySQL 8.2.0 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `asistencias`;
CREATE TABLE `asistencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_evento` int DEFAULT '1',
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_usuario` (`id_usuario`,`id_evento`),
  CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `asistencias_qr`;
CREATE TABLE `asistencias_qr` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_hora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `asistencias_qr_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `certificados`;
CREATE TABLE `certificados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `nombre_evento` varchar(255) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `archivo_certificado` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `ejes`;
CREATE TABLE `ejes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `evaluador_eje`;
CREATE TABLE `evaluador_eje` (
  `id_usuario` int NOT NULL,
  `id_eje` int NOT NULL,
  PRIMARY KEY (`id_usuario`,`id_eje`),
  KEY `id_eje` (`id_eje`),
  CONSTRAINT `evaluador_eje_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `evaluador_eje_ibfk_2` FOREIGN KEY (`id_eje`) REFERENCES `ejes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_email` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `ponencia_evaluador`;
CREATE TABLE `ponencia_evaluador` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_ponencia` int NOT NULL,
  `id_evaluador` int NOT NULL,
  `evaluacion` text,
  `fecha_evaluacion` datetime DEFAULT NULL,
  `estado` enum('aprobada','desaprobada') DEFAULT NULL,
  `orden` tinyint DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_ponencia` (`id_ponencia`),
  KEY `id_evaluador` (`id_evaluador`),
  CONSTRAINT `ponencia_evaluador_ibfk_1` FOREIGN KEY (`id_ponencia`) REFERENCES `ponencias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ponencia_evaluador_ibfk_2` FOREIGN KEY (`id_evaluador`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `ponencias`;
CREATE TABLE `ponencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_eje` int NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `fecha_subida` datetime DEFAULT CURRENT_TIMESTAMP,
  `fue_evaluada` tinyint(1) DEFAULT '0',
  `comentario` text,
  `universidad` varchar(255) DEFAULT NULL,
  `autores_colaboradores` text,
  `palabras_clave` text,
  `resumen` text,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_eje` (`id_eje`),
  CONSTRAINT `ponencias_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ponencias_ibfk_2` FOREIGN KEY (`id_eje`) REFERENCES `ejes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `qr_tokens`;
CREATE TABLE `qr_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(100) NOT NULL,
  `id_evento` int NOT NULL DEFAULT '1',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `valido` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `usuario_roles`;
CREATE TABLE `usuario_roles` (
  `id_usuario` int NOT NULL,
  `id_rol` int NOT NULL,
  PRIMARY KEY (`id_usuario`,`id_rol`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuario_roles_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `usuario_roles_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE notificaciones 
MODIFY COLUMN usuario_email VARCHAR(100) NULL;

ALTER TABLE notificaciones ADD COLUMN tipo ENUM('general', 'personal') DEFAULT 'general';
-- 2025-06-16 19:03:06 UTC







































-- datos apra ingresa--
-- Adminer 5.0.5 MySQL 8.2.0 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;




INSERT INTO `ejes` (`id`, `nombre`) VALUES
(1,	'Innovación tecnológica'),
(2,	'Gestión del conocimiento'),
(3,	'Transferencia tecnológica'),
(4,	'Emprendedurismo e innovación'),
(5,	'Educación y tecnología');

INSERT INTO `evaluador_eje` (`id_usuario`, `id_eje`) VALUES
(6,	1),
(9,	1),
(11,	1),
(6,	2),
(7,	2),
(8,	2),
(9,	2),
(11,	2),
(12,	2),
(6,	3),
(7,	3),
(8,	3),
(9,	3),
(11,	3),
(12,	3),
(6,	4),
(7,	4),
(8,	4),
(9,	4),
(11,	4),
(12,	4),
(6,	5),
(7,	5),
(8,	5),
(9,	5),
(11,	5),
(12,	5);


INSERT INTO `ponencia_evaluador` (`id`, `id_ponencia`, `id_evaluador`, `evaluacion`, `fecha_evaluacion`, `estado`, `orden`) VALUES
(12,	7,	9,	NULL,	NULL,	NULL,	1),
(13,	7,	12,	NULL,	NULL,	NULL,	2);

INSERT INTO `ponencias` (`id`, `id_usuario`, `id_eje`, `archivo`, `fecha_subida`, `fue_evaluada`, `comentario`, `universidad`, `autores_colaboradores`, `palabras_clave`, `resumen`) VALUES
(6,	7,	2,	'ponencia_e57cc5ca.pdf',	'2025-06-16 15:54:29',	0,	NULL,	'a876',	'mariana.perez@example.com',	'uwu',	'nashe'),
(7,	7,	3,	'ponencia_b7825a49.pdf',	'2025-06-16 15:58:59',	0,	NULL,	'a',	'a',	'a',	'a'),
(8,	7,	2,	'ponencia_893937a7.pdf',	'2025-06-16 15:59:29',	0,	NULL,	'a',	'a',	'a',	'a');


INSERT INTO `roles` (`id`, `nombre`) VALUES
(1,	'admin'),
(4,	'asistente'),
(3,	'evaluador'),
(5,	'expositor'),
(2,	'ponente');

INSERT INTO `usuario_roles` (`id_usuario`, `id_rol`) VALUES
(7,	1),
(1,	2),
(5,	2),
(6,	2),
(7,	2),
(8,	2),
(9,	2),
(10,	2),
(11,	2),
(12,	2),
(5,	3),
(6,	3),
(7,	3),
(8,	3),
(9,	3),
(11,	3),
(12,	3);

INSERT INTO `usuarios` (`id`, `username`, `password`, `nombre`, `apellido`, `dni`, `email`, `telefono`) VALUES
(1,	'ponente@ejemplo.com',	'$2y$10$2u5zF4jXaZhnXAoEyNSe.eUz7PlQUaHoU8hQAV2hArsPvC8I4b79y',	'Laura',	'Gómez',	'87654321',	'ponente@ejemplo.com',	'987654321'),
(5,	'eva1',	'$2y$12$Kl4ZeJ.w0OC9e30jaPR/7e4MqYl/4/w/7MTNFdgP4DIray5JAsYzO',	'Carlos',	'Martínez',	'11111111',	'eva1@example.com',	'111111'),
(6,	'eva2',	'$2y$12$Kl4ZeJ.w0OC9e30jaPR/7e4MqYl/4/w/7MTNFdgP4DIray5JAsYzO',	'Lucía',	'Fernández',	'22222222',	'eva2@example.com',	'222222'),
(7,	'eva3',	'$2y$12$Kl4ZeJ.w0OC9e30jaPR/7e4MqYl/4/w/7MTNFdgP4DIray5JAsYzO',	'Martín',	'López',	'33333333',	'eva3@example.com',	'333333'),
(8,	'luciam',	'1234',	'Lucía',	'Martínez',	'30123456',	'lucia.martinez@example.com',	'1122334455'),
(9,	'carlosg',	'1234',	'Carlos',	'Gómez',	'30234567',	'carlos.gomez@example.com',	'1133445566'),
(10,	'marianap',	'1234',	'Mariana',	'Pérez',	'30345678',	'mariana.perez@example.com',	'1144556677'),
(11,	'diegos',	'1234',	'Diego',	'Suárez',	'30456789',	'diego.suarez@example.com',	'1155667788'),
(12,	'florencial',	'1234',	'Florencia',	'López',	'30567890',	'flor.lopez@example.com',	'1166778899');

-- 2025-06-16 19:07:09 UTC