<?php
// GET (opcional ?id_categoria=1) -> {"rpta":true,"productos":[...]}
require __DIR__ . '/base.php';
exige_metodo('GET');
$sql = 'SELECT p.id_producto, p.id_categoria, c.nombre AS categoria, p.nombre, p.precio, p.stock
        FROM producto p JOIN categoria c ON c.id_categoria = p.id_categoria
        WHERE p.activo = 1';
$params = [];
if (isset($_GET['id_categoria']) && $_GET['id_categoria'] !== '') {
    $sql .= ' AND p.id_categoria = ?';
    $params[] = (int)$_GET['id_categoria'];
}
$st = db()->prepare($sql . ' ORDER BY p.nombre');
$st->execute($params);
$filas = $st->fetchAll();
foreach ($filas as &$f) {
    $f['id_producto'] = (int)$f['id_producto'];
    $f['id_categoria'] = (int)$f['id_categoria'];
    $f['precio'] = (float)$f['precio'];
    $f['stock'] = (int)$f['stock'];
}
responder(['rpta' => true, 'productos' => $filas]);
