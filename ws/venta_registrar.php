<?php
// Sincronización desde la app (OBJ 5): la app guarda ventas en SQLite y luego las envía.
// POST JSON: {"codigo_local":"A1","id_usuario":2,"id_cliente":1|null,"fecha":"2026-10-08 18:30:00",
//             "detalles":[{"id_producto":1,"cantidad":2}]}
// El precio lo toma el servidor desde la tabla producto. "codigo_local" evita duplicar una venta reenviada.
require __DIR__ . '/base.php';
exige_metodo('POST');
$d = cuerpo_json();

$idUsuario = (int)($d['id_usuario'] ?? 0);
$idCliente = (isset($d['id_cliente']) && $d['id_cliente'] !== '') ? (int)$d['id_cliente'] : null;
$codigo = isset($d['codigo_local']) ? substr(trim((string)$d['codigo_local']), 0, 40) : '';
$codigo = $codigo === '' ? null : $codigo;
$detalles = $d['detalles'] ?? [];

if ($idUsuario <= 0 || !is_array($detalles) || count($detalles) === 0 || count($detalles) > 100) {
    responder(['rpta' => false, 'mensaje' => 'Datos de la venta incompletos'], 422);
}
$fecha = null;
if (!empty($d['fecha'])) {
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', (string)$d['fecha']);
    if ($dt && $dt->format('Y-m-d H:i:s') === $d['fecha']) $fecha = $d['fecha'];
}

$pdo = db();

if ($codigo !== null) {
    $st = $pdo->prepare('SELECT id_venta, total FROM venta WHERE codigo_local = ?');
    $st->execute([$codigo]);
    if ($v = $st->fetch()) {
        responder(['rpta' => true, 'id_venta' => (int)$v['id_venta'], 'total' => (float)$v['total'], 'mensaje' => 'La venta ya estaba registrada']);
    }
}

$st = $pdo->prepare('SELECT 1 FROM usuario WHERE id_usuario = ? AND activo = 1');
$st->execute([$idUsuario]);
if (!$st->fetch()) {
    responder(['rpta' => false, 'mensaje' => 'Usuario no válido'], 422);
}

try {
    $pdo->beginTransaction();
    $pdo->prepare('INSERT INTO venta (id_cliente, id_usuario, fecha, total, codigo_local) VALUES (?, ?, COALESCE(?, NOW()), 0, ?)')
        ->execute([$idCliente, $idUsuario, $fecha, $codigo]);
    $idVenta = (int)$pdo->lastInsertId();

    $total = 0.0;
    $selP = $pdo->prepare('SELECT precio FROM producto WHERE id_producto = ? AND activo = 1 FOR UPDATE');
    $updS = $pdo->prepare('UPDATE producto SET stock = stock - ? WHERE id_producto = ? AND stock >= ?');
    $insD = $pdo->prepare('INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unit, subtotal) VALUES (?, ?, ?, ?, ?)');

    foreach ($detalles as $it) {
        $idp = (int)($it['id_producto'] ?? 0);
        $cant = (int)($it['cantidad'] ?? 0);
        if ($idp <= 0 || $cant <= 0) throw new RuntimeException('Detalle inválido');
        $selP->execute([$idp]);
        $p = $selP->fetch();
        if (!$p) throw new RuntimeException("El producto $idp no existe o está inactivo");
        $updS->execute([$cant, $idp, $cant]);
        if ($updS->rowCount() === 0) throw new RuntimeException("Stock insuficiente para el producto $idp");
        $sub = round((float)$p['precio'] * $cant, 2);
        $insD->execute([$idVenta, $idp, $cant, $p['precio'], $sub]);
        $total += $sub;
    }
    $pdo->prepare('UPDATE venta SET total = ? WHERE id_venta = ?')->execute([round($total, 2), $idVenta]);
    $pdo->commit();
    responder(['rpta' => true, 'id_venta' => $idVenta, 'total' => round($total, 2), 'mensaje' => 'Venta registrada']);
} catch (RuntimeException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    responder(['rpta' => false, 'mensaje' => $e->getMessage()], 422);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    throw $e;
}
