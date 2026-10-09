<?php
// POST JSON: {"usuario":"...","password":"..."}
require __DIR__ . '/base.php';
exige_metodo('POST');
$d = cuerpo_json();
$usuario = trim((string)($d['usuario'] ?? ''));
$password = (string)($d['password'] ?? '');
if ($usuario === '' || $password === '') {
    responder(['rpta' => false, 'mensaje' => 'Ingrese usuario y contraseña'], 422);
}
$st = db()->prepare('SELECT id_usuario, nombres, rol, clave FROM usuario WHERE usuario = ? AND activo = 1');
$st->execute([$usuario]);
$u = $st->fetch();
if (!$u || !password_verify($password, $u['clave'])) {
    responder(['rpta' => false, 'mensaje' => 'Usuario o contraseña incorrectos'], 401);
}
responder([
    'rpta' => true,
    'id_usuario' => (int)$u['id_usuario'],
    'nombres' => $u['nombres'],
    'rol' => $u['rol'],
    'mensaje' => 'Login exitoso',
]);
