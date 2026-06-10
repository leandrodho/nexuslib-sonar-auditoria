USE bd_nexus;

CREATE TABLE inventory (
    registro INT PRIMARY KEY,
    codigo VARCHAR(100),
    titulo TEXT NOT NULL,
    autor VARCHAR(255),
    biblioteca VARCHAR(100),
    tipo VARCHAR(50),
    procedencia VARCHAR(50),
    fecha DATETIME,
    estado VARCHAR(20) DEFAULT 'Disponible'
);