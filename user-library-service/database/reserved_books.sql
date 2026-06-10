CREATE TABLE reserved_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_uuid CHAR(36) NOT NULL COMMENT 'UUID proveniente del auth-service',
    codigo VARCHAR(100) NOT NULL COMMENT 'Código general del libro',
    registro INT(11) NOT NULL COMMENT 'Registro exacto del ejemplar físico',
    estado VARCHAR(50) DEFAULT 'Reservado',
    fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_reserved (user_uuid, codigo, registro)
);