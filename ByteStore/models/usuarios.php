<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function crear(string $nombre, string $email, string $password): int {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare(
            "INSERT INTO USUARIO (nombre, email, password) VALUES (:nombre, :email, :password)" // ✅ :email
        );
        $stmt->execute([
            ':nombre'   => $nombre,
            ':email'    => $email,
            ':password' => $hash,
        ]);
        return (int) $this->pdo->lastInsertId(); // ✅ -> no -
    }

    public function login(string $email, string $password): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM USUARIO WHERE email = :email LIMIT 1"
        );
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            unset($usuario['password']);
            return $usuario; // ✅ return dentro del if
        }
        return false;
    }

    public function buscarPorId(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre, email FROM USUARIO WHERE id = :id" // ✅ campo id
        );
        $stmt->execute([':id' => $id]); // ✅ parámetro con valor
        return $stmt->fetch();
    }
}