<?php
// GET -> {"rpta":true,"clientes":[{id_cliente,nombres,telefono}]}
require __DIR__ . '/base.php';
exige_metodo('GET');
$filas = db()->query('SELECT id_cliente, nombres, telefono FROM cliente ORDER BY nombres')->fetchAll();
responder(['rpta' => true, 'clientes' => $filas]);
