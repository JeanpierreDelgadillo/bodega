<?php
// GET -> {"rpta":true,"categorias":[{id_categoria,nombre}]}
require __DIR__ . '/base.php';
exige_metodo('GET');
$filas = db()->query('SELECT id_categoria, nombre FROM categoria ORDER BY nombre')->fetchAll();
responder(['rpta' => true, 'categorias' => $filas]);
