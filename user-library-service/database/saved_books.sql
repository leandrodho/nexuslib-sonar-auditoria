CREATE TABLE saved_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_uuid CHAR(36) NOT NULL,
    codigo VARCHAR(100) NOT NULL,
    origen VARCHAR(50) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    portada_url VARCHAR(500) DEFAULT NULL,
    fecha_guardado DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_saved (user_uuid, codigo, origen)
);