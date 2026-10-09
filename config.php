<?php
// === Datos de conexión: credenciales de InfinityFree ===
const DB_HOST = 'sql306.infinityfree.com';        // Reemplaza por el MySQL Hostname de tu panel
const DB_NAME = 'if0_43128283_bodega';            // Reemplaza por el nombre exacto con prefijo
const DB_USER = 'if0_43128283';                   // Tu usuario de InfinityFree
const DB_PASS = 'TU_CONTRASEÑA_DEL_HOSTING';      // La clave que creaste para esta cuenta

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}

function h($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}