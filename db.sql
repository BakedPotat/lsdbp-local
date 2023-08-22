-- Crear la base de datos
CREATE DATABASE test;

-- Usar la base de datos creada
USE test;

-- Crear la tabla de usuarios
CREATE TABLE usuarios (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL
);




