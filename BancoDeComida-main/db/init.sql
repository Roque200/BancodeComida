CREATE TABLE roles (id INT PRIMARY KEY, nombre VARCHAR(50));
INSERT INTO roles VALUES (1, 'Administrador'), (2, 'Coordi Alimentos'), (3, 'Coordi Rutas');

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    password_hash VARCHAR(255),
    role_id INT,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- El usuario es 'admin' y la contraseña es 'password' (ya cifrada)
INSERT INTO users (username, password_hash, role_id) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

