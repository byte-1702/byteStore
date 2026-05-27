<?php
declare(strict_types=1);
session_start();

// ── Solo acepta POST ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit;
}

// ── Validación CSRF ──────────────────────────────────────────────
if (
    empty($_POST['csrf_token']) ||
    !hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) $_POST['csrf_token'])
) {
    $_SESSION['registro_error'] = 'Token de seguridad inválido. Recarga la página e intenta de nuevo.';
    header('Location: registro.php');
    exit;
}

// ── Leer y limpiar campos ────────────────────────────────────────
$nombre   = trim((string) ($_POST['nombre']            ?? ''));
$usuario  = trim((string) ($_POST['usuario']           ?? ''));
$correo   = trim((string) ($_POST['correo']            ?? ''));
$password = (string)      ($_POST['password']          ?? '');
$confirm  = (string)      ($_POST['confirm_password']  ?? '');
$terminos = isset($_POST['terminos']);

// ── Validaciones del servidor ────────────────────────────────────
$errors = [];

if ($nombre === '') {
    $errors[] = 'El nombre completo es obligatorio.';
}

if (strlen($usuario) < 4) {
    $errors[] = 'El nombre de usuario debe tener al menos 4 caracteres.';
} elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $usuario)) {
    $errors[] = 'El nombre de usuario solo puede contener letras, números y guiones bajos.';
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El correo electrónico no es válido.';
}

if (strlen($password) < 8) {
    $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
}

if ($password !== $confirm) {
    $errors[] = 'Las contraseñas no coinciden.';
}

if (!$terminos) {
    $errors[] = 'Debes aceptar los términos y condiciones.';
}

if (!empty($errors)) {
    $_SESSION['registro_error'] = implode(' ', $errors);
    header('Location: registro.php');
    exit;
}

// ── Verificar duplicados ─────────────────────────────────────────
$existingUsers = $_SESSION['registered_users'] ?? [];

// También incluir usuarios demo para evitar conflictos
$demoUsers = ['demo', 'admin'];
$demoEmails = ['demo@gamestore.com', 'admin@gamestore.com'];

if (in_array(strtolower($usuario), $demoUsers, true)) {
    $_SESSION['registro_error'] = 'Ese nombre de usuario ya está en uso. Elige otro.';
    header('Location: registro.php');
    exit;
}

foreach ($existingUsers as $u) {
    if (strtolower((string) ($u['username'] ?? '')) === strtolower($usuario)) {
        $_SESSION['registro_error'] = 'Ese nombre de usuario ya está en uso. Elige otro.';
        header('Location: registro.php');
        exit;
    }
    if (strtolower((string) ($u['email'] ?? '')) === strtolower($correo)) {
        $_SESSION['registro_error'] = 'Ese correo ya está registrado. Intenta iniciar sesión.';
        header('Location: registro.php');
        exit;
    }
}

// ── Guardar nuevo usuario ────────────────────────────────────────
// NOTA: En producción se usaría password_hash($password, PASSWORD_DEFAULT)
// y se almacenaría en una base de datos MySQL.
$_SESSION['registered_users'][] = [
    'username' => $usuario,
    'email'    => strtolower($correo),
    'password' => $password,
    'nombre'   => $nombre,
    'role'     => 'customer',
];

// ── Auto-login tras el registro ──────────────────────────────────
$_SESSION['user'] = [
    'username' => $usuario,
    'nombre'   => $nombre,
    'email'    => strtolower($correo),
    'role'     => 'customer',
];

unset($_SESSION['registro_error']);
$_SESSION['registro_success'] = '¡Cuenta creada con éxito! Bienvenido/a, ' .
    htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '.';

header('Location: index.php');
exit;
