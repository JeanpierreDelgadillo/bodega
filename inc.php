<?php

require __DIR__ . '/config.php';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

function requiere_login(): void
{
    if (empty($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }
}

function csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_ok(): bool
{
    return hash_equals(
        $_SESSION['csrf'] ?? '',
        $_POST['csrf'] ?? ''
    );
}

function cabecera(string $titulo, bool $menu = true): void
{
    $T = require __DIR__ . '/tablas.php';

    echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>' . h($titulo) . ' - Bodega Don Pepe</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        href="css/estilos.css"
        rel="stylesheet"
    >
</head>

<body>';
    if ($menu) {
        echo '
<nav class="navbar navbar-expand-lg navbar-dark navbar-bodega">
    <div class="container-fluid px-3 px-lg-4">
        <a
            class="navbar-brand marca-bodega mb-0"
            href="index.php"
        >
            <div class="contenedor-logo">
                <img
                    src="img/logo.png"
                    alt="Logo Bodega Don Pepe"
                >
            </div>

            <span>
                BODEGA
                <span class="nombre-don-pepe">
                    DON PEPE
                </span>
            </span>

        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuBodega"
            aria-controls="menuBodega"
            aria-expanded="false"
            aria-label="Mostrar menú"
        >

            <span class="navbar-toggler-icon"></span>

        </button>

        <div
            class="collapse navbar-collapse"
            id="menuBodega"
        >
            <div
                class="
                    navbar-nav
                    ms-auto
                    align-items-lg-center
                "
            >';
        foreach ($T as $k => $d) {

            echo '

                <a
                    class="nav-link"
                    href="crud.php?t=' . h($k) . '"
                >
                    ' . h($d['titulo']) . '
                </a>';
        }
        echo '
                <a
                    class="
                        btn
                        btn-sm
                        btn-outline-danger
                        boton-salir
                        ms-lg-2
                    "
                    href="logout.php"
                >
                    Salir
                </a>

            </div>

        </div>

    </div>

</nav>';

    }

    echo '

<div class="container contenido-principal">';

}


function pie(): void
{
    echo '

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>';
}