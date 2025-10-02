-- Base de datos simplificada para el sistema de certificados
CREATE DATABASE IF NOT EXISTS certificados_db;
USE certificados_db;

-- Tabla de participantes
CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    course VARCHAR(255) NOT NULL,
    date_completed DATE NOT NULL,
    certificate_generated BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Índices para optimizar búsquedas
CREATE INDEX idx_email ON participants(email);
CREATE INDEX idx_course ON participants(course);
CREATE INDEX idx_certificate_generated ON participants(certificate_generated);
