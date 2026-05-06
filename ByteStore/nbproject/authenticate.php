<?php
declare(strict_types=1);
session_start();

// ── Solo acepta POST ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// ── Validación CSRF ──────────────────────────────────────────────
if (
    empty($_POST['csrf_token']) ||
    !hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) $_POST['csrf_token'])
) {
    $_SESSION['login_error'] = 'Token de seguridad inválido. Por favor recarga la página e intenta de nuevo.';
    header('Location: login.php');
    exit;
}

// ── Leer datos del formulario ────────────────────────────────────
$identifier = trim((string) ($_POST['identifier'] ?? ''));
$password   = (string) ($_POST['password'] ?? '');

if ($identifier === '' || $password === '') {
    $_SESSION['login_error'] = 'Por favor completa todos los campos.';
    header('Location: login.php');
    exit;
}

// ── Base de usuarios ─────────────────────────────────────────────
// Usuarios demo predefinidos (sin base de datos)
// En un proyecto real se consultaría MySQL con password_verify()
$demoUsers = [
    [
        'username' => 'demo',
        'email'    => 'demo@gamestore.com',
        'password' => 'demo1234',
        'nombre'   => 'Usuario Demo',
    ],
    [
        'username' => 'admin',
        'email'    => 'admin@gamestore.com',
        'password' => 'admin1234',
        'nombre'   => 'Administrador',
    ],
];

// Usuarios registrados durante la sesión actual
$sessionUsers = $_SESSION['registered_users'] ?? [];
$allUsers     = array_merge($demoUsers, $sessionUsers);

// ── Buscar coincidencia ──────────────────────────────────────────
$found = null;
foreach ($allUsers as $user) {
    $matchIdentifier = ($user['username'] === $identifier || $user['email'] === $identifier);
    $matchPassword   = ($user['password'] === $password);
    if ($matchIdentifier && $matchPassword) {
        $found = $user;
        break;
    }
}

// ── Resultado ────────────────────────────────────────────────────
if ($found !== null) {
    // Login exitoso: guardar datos básicos en sesión
    $_SESSION['user'] = [
        'username' => $found['username'],
        'nombre'   => $found['nombre'],
        'email'    => $found['email'],
    ];
    unset($_SESSION['login_error']);
    header('Location: index.php');
    exit;
}

// Login fallido
$_SESSION['login_error'] = 'Usuario/correo o contraseña incorrectos. Verifica tus datos e intenta de nuevo.';
header('Location: login.php');
exit;
