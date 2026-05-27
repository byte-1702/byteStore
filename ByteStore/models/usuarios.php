<?php

require_once __DIR__ . '/../config/database.php';

class usuario {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function crear(string $nombre, string $email, string $password): int {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare(
            "INSERT INTO USUARIO (nombre, email, password) VALUES (:nombre, email, :password)"

        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $hash,
        ]);
        return (int) $this->pdo-lastInsertId();
    }
    
    public function login(string $email, string $password): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM USUARIO WHERE email = :email LIMIT 1"
        );
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            unset($usuario['password']);
        }
        return false;
    }

    public function buscarporId(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT id_usuario, nombre, email FROM USUARIO WHERE id_usuario = :id_usuario"
        );
        $stmt->execute([':id_usuario']);
        return $stmt->fetch();
    }
}