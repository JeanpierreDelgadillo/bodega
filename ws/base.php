<?php
// Utilidades comunes de los servicios REST (todas las respuestas son JSON con "rpta" y "mensaje").
require __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

function responder(array $datos, int $codigo = 200): void {
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function exige_metodo(string $metodo): void {
    if ($_SERVER['REQUEST_METHOD'] !== $metodo) {
        responder(['rpta' => false, 'mensaje' => 'Método no permitido'], 405);
    }
}

function cuerpo_json(): array {
    $j = json_decode(file_get_contents('php://input'), true);
    return is_array($j) ? $j : [];
}

set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage());
    responder(['rpta' => false, 'mensaje' => 'Error en el servidor'], 500);
});
