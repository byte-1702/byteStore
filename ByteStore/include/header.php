<?php
// Iniciar sesión si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/icons.php';

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'ByteStore Academy', ENT_QUOTES, 'UTF-8'); ?></title>

    <!-- Estilos globales (reset, header, nav, footer, botones) -->
    <link rel="stylesheet" href="../CSS/global.css">

    <!-- Estilos específicos de esta página -->
    <?php if (!empty($cssPath)): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
</head>

<body>

<header>
    <h1><?php echo htmlspecialchars($pageTitle ?? 'ByteStore Academy', ENT_QUOTES, 'UTF-8'); ?></h1>
    <?php include __DIR__ . '/nav.php'; ?>
</header>

<main>
