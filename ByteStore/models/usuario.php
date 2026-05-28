<?php

require_once(__DIR__ . '/../config/database.php');

class Usuario {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Registra un nuevo usuario
     */
    public function crear(string $nombre, string $email, string $password): int {
        try {
            // Validar email único
            if ($this->emailExiste($email)) {
                throw new Exception("El email ya está registrado");
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare(
                "INSERT INTO usuario (nombre, email, password) VALUES (:nombre, :email, :password)"
            );
            $stmt->execute([
                ':nombre'   => $nombre,
                ':email'    => $email,
                ':password' => $hash,
            ]);
            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error al crear usuario: " . $e->getMessage());
        }
    }

    /**
     * Login - verifica credenciales
     */
    public function login(string $email, string $password): array|false {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, nombre, email FROM usuario WHERE email = :email LIMIT 1"
            );
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return false; // Usuario no existe
            }

            // Obtener el hash para verificación
            $stmt = $this->pdo->prepare(
                "SELECT password FROM usuario WHERE id = :id"
            );
            $stmt->execute([':id' => $usuario['id']]);
            $passwordData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($passwordData && password_verify($password, $passwordData['password'])) {
                return $usuario; // Credenciales correctas, devolver sin el hash
            }

            return false; // Contraseña incorrecta
        } catch (PDOException $e) {
            throw new Exception("Error en login: " . $e->getMessage());
        }
    }

    /**
     * Busca un usuario por ID
     */
    public function buscarPorId(int $id): array|false {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, nombre, email FROM usuario WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al buscar usuario: " . $e->getMessage());
        }
    }

    /**
     * Verifica si un email existe
     */
    public function emailExiste(string $email): bool {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id FROM usuario WHERE email = :email LIMIT 1"
            );
            $stmt->execute([':email' => $email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            throw new Exception("Error al verificar email: " . $e->getMessage());
        }
    }

    /**
     * Actualiza la contraseña de un usuario
     */
    public function cambiarPassword(int $id, string $passwordActual, string $passwordNueva): bool {
        try {
            // Verificar contraseña actual
            $stmt = $this->pdo->prepare("SELECT password FROM usuario WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data || !password_verify($passwordActual, $data['password'])) {
                throw new Exception("Contraseña actual incorrecta");
            }

            // Actualizar con nueva contraseña
            $hash = password_hash($passwordNueva, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare(
                "UPDATE usuario SET password = :password WHERE id = :id"
            );
            return $stmt->execute([
                ':password' => $hash,
                ':id'       => $id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al cambiar password: " . $e->getMessage());
        }
    }

    /**
     * Elimina un usuario
     */
    public function eliminar(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM usuario WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar usuario: " . $e->getMessage());
        }
    }
}
