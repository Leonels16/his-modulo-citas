CREATE DATABASE IF NOT EXISTS his_db;
USE his_db;

CREATE TABLE IF NOT EXISTS doctores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    paciente_id INT NOT NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'atendida') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctores(id),
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
);

-- Semillas iniciales requeridas por la rúbrica
INSERT INTO doctores (nombre, especialidad) VALUES 
('Dr. Carlos Mendoza', 'Medicina General'),
('Dra. Sofia Ramos', 'Pediatría'),
('Dr. Roberto Gómez', 'Cardiología');

INSERT INTO pacientes (nombre, telefono) VALUES 
('Oscar Perez', '55512345'),
('Maria Lopez', '55598765'),
('Juan Ramirez', '55533221');