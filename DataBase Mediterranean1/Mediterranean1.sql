CREATE DATABASE MEDITERRANEAN;
USE MEDITERRANEAN;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(100) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sucursales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    codigo_sucursal INT UNIQUE NOT NULL
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    cantidad INT DEFAULT 0,
    id_sucursal INT,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE SET NULL
);

CREATE TABLE Utilidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    id_sucursal INT NOT NULL,
    cantidad INT NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE CASCADE
);

CREATE TABLE reportes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL,
    fecha DATE NOT NULL,
    id_sucursal INT,
    descripcion TEXT,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE SET NULL
);

INSERT INTO sucursales (nombre, codigo_sucursal) VALUES 
('Sucursal Kenneddy', 101),
('Sucursal Usme', 102);

INSERT INTO productos (nombre, cantidad, id_sucursal) VALUES 
('Tornillos Drywall', 50, 1),
('Tornillos Lamina', 30, 2);

INSERT INTO usuarios (nombre_usuario, correo, contrasena) VALUES
('admin', 'pedro_guevara_adso_sena@mediterranean.com', 'Peter1992');

