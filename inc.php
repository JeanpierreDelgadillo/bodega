<?php
require __DIR__ . '/config.php';
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

function requiere_login(): void {
    if (empty($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }
}

function csrf(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_ok(): bool {
    return hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '');
}

function cabecera(string $titulo, bool $menu = true): void {
    $T = require __DIR__ . '/tablas.php';
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<title>' . h($titulo) . ' - Bodega Don Pepe</title>'
       . '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">'
       . '</head><body class="bg-light">';
    if ($menu) {
        echo '<nav class="navbar navbar-expand-lg navbar-dark bg-success"><div class="container-fluid">'
           . '<a class="navbar-brand" href="index.php">Bodega Don Pepe</a><div class="navbar-nav flex-row flex-wrap gap-3">';
        foreach ($T as $k => $d) {
            echo '<a class="nav-link" href="crud.php?t=' . h($k) . '">' . h($d['titulo']) . '</a>';
        }
        echo '<a class="nav-link" href="logout.php">Salir (' . h($_SESSION['nombre'] ?? '') . ')</a></div></div></nav>';
    }
    echo '<div class="container py-4">';
}

function pie(): void {
    echo '</div></body></html>';
}
