CREATE DATABASE IF NOT EXISTS lince_motos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lince_motos;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS mantenimientos;
DROP TABLE IF EXISTS motocicletas;
DROP TABLE IF EXISTS usuarios;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    rol ENUM('Administrador','Jefe de taller','Mecánico','Supervisor','Consulta') NOT NULL DEFAULT 'Consulta',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE motocicletas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_qr VARCHAR(50) NOT NULL UNIQUE,
    marca VARCHAR(80) NOT NULL,
    modelo VARCHAR(80) NOT NULL,
    anio SMALLINT UNSIGNED NULL,
    placa VARCHAR(30) NOT NULL UNIQUE,
    numero_motor VARCHAR(100) NULL UNIQUE,
    numero_chasis VARCHAR(100) NULL UNIQUE,
    unidad_asignada VARCHAR(150) NOT NULL,
    fecha_ingreso DATE NULL,
    kilometraje_actual INT UNSIGNED NOT NULL DEFAULT 0,
    tipo_mantenimiento ENUM('Por kilometraje','Por tiempo','Mixto') NOT NULL DEFAULT 'Mixto',
    estado ENUM('Operativa','En mantenimiento','Reparación','Colisión','Trámite de descarte','Fuera del sistema') NOT NULL DEFAULT 'Operativa',
    observaciones TEXT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_moto_estado (estado),
    INDEX idx_moto_unidad (unidad_asignada)
) ENGINE=InnoDB;

CREATE TABLE mantenimientos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    moto_id INT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    kilometraje INT UNSIGNED NOT NULL,
    tipo ENUM('Preventivo','Correctivo','Reparación por daño','Colisión','Inspección') NOT NULL,
    diagnostico TEXT NULL,
    trabajos_realizados TEXT NULL,
    repuestos_utilizados TEXT NULL,
    responsable VARCHAR(150) NOT NULL,
    proximo_km INT UNSIGNED NULL,
    proxima_fecha DATE NULL,
    estado ENUM('En proceso','Finalizado','Pendiente de repuesto','Pendiente de presupuesto') NOT NULL DEFAULT 'En proceso',
    creado_por INT UNSIGNED NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mantenimiento_moto FOREIGN KEY (moto_id) REFERENCES motocicletas(id) ON DELETE CASCADE,
    CONSTRAINT fk_mantenimiento_usuario FOREIGN KEY (creado_por) REFERENCES usuarios(id),
    INDEX idx_mantenimiento_fecha (fecha),
    INDEX idx_mantenimiento_proximo (proxima_fecha, proximo_km)
) ENGINE=InnoDB;

INSERT INTO usuarios (nombre, correo, clave, rol) VALUES
('Administrador LINCE', 'admin@lince.local', '$2y$12$oo9KfzB8mg0OSDgLvAhYyOCMGNVHJ812.puxTIOxynRjJqbzg9gdO', 'Administrador');

INSERT INTO motocicletas
(codigo_qr, marca, modelo, anio, placa, numero_motor, numero_chasis, unidad_asignada, fecha_ingreso, kilometraje_actual, tipo_mantenimiento, estado, observaciones)
VALUES
('LINCE-M-001', 'Suzuki', 'DR 150', 2025, 'PN-1001', 'MOT-1001', 'CHA-1001', 'LINCE San Miguelito', '2025-01-15', 6800, 'Por kilometraje', 'Operativa', 'Motocicleta de demostración'),
('LINCE-M-002', 'Suzuki', 'DR 200', 2020, 'PN-1002', 'MOT-1002', 'CHA-1002', 'LINCE San Miguelito', '2020-06-10', 15200, 'Mixto', 'En mantenimiento', 'Pendiente de revisión general');

INSERT INTO mantenimientos
(moto_id, fecha, kilometraje, tipo, diagnostico, trabajos_realizados, repuestos_utilizados, responsable, proximo_km, proxima_fecha, estado, creado_por)
VALUES
(1, '2026-07-10', 6800, 'Preventivo', 'Revisión programada', 'Cambio de aceite y ajuste de cadena', 'Aceite 10W40', 'Mecánico de prueba', 7000, NULL, 'Finalizado', 1),
(2, '2026-07-10', 15200, 'Correctivo', 'Desgaste en sistema de frenos', 'Diagnóstico inicial', NULL, 'Mecánico de prueba', NULL, '2026-10-10', 'Pendiente de repuesto', 1);
