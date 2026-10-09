<?php
// Definición de las tablas que se mantienen desde el portal (una sola pantalla genérica las gestiona).
// t = tipo: text | password | number | select | fk | bool | datetime (solo lista)
return [
    'usuario' => [
        'titulo' => 'Usuarios', 'pk' => 'id_usuario', 'buscar' => ['nombres', 'usuario'],
        'campos' => [
            ['n' => 'nombres', 'l' => 'Nombres', 't' => 'text', 'req' => 1],
            ['n' => 'usuario', 'l' => 'Usuario', 't' => 'text', 'req' => 1],
            ['n' => 'clave', 'l' => 'Contraseña', 't' => 'password'],
            ['n' => 'rol', 'l' => 'Rol', 't' => 'select', 'req' => 1, 'op' => ['admin' => 'Administrador', 'vendedor' => 'Vendedor']],
            ['n' => 'activo', 'l' => 'Activo', 't' => 'bool'],
        ],
    ],
    'categoria' => [
        'titulo' => 'Categorías', 'pk' => 'id_categoria', 'buscar' => ['nombre'],
        'campos' => [
            ['n' => 'nombre', 'l' => 'Nombre', 't' => 'text', 'req' => 1],
        ],
    ],
    'producto' => [
        'titulo' => 'Productos', 'pk' => 'id_producto', 'buscar' => ['nombre'],
        'campos' => [
            ['n' => 'id_categoria', 'l' => 'Categoría', 't' => 'fk', 'req' => 1, 'tabla' => 'categoria', 'pk' => 'id_categoria', 'mostrar' => 'nombre'],
            ['n' => 'nombre', 'l' => 'Nombre', 't' => 'text', 'req' => 1],
            ['n' => 'precio', 'l' => 'Precio (S/)', 't' => 'number', 'step' => '0.01', 'req' => 1],
            ['n' => 'stock', 'l' => 'Stock', 't' => 'number', 'step' => '1', 'req' => 1],
            ['n' => 'activo', 'l' => 'Activo', 't' => 'bool'],
        ],
    ],
    'cliente' => [
        'titulo' => 'Clientes', 'pk' => 'id_cliente', 'buscar' => ['nombres', 'telefono'],
        'campos' => [
            ['n' => 'nombres', 'l' => 'Nombres', 't' => 'text', 'req' => 1],
            ['n' => 'telefono', 'l' => 'Teléfono', 't' => 'text'],
        ],
    ],
    'venta' => [
        'titulo' => 'Ventas', 'pk' => 'id_venta', 'buscar' => [],
        'campos' => [
            ['n' => 'id_cliente', 'l' => 'Cliente', 't' => 'fk', 'tabla' => 'cliente', 'pk' => 'id_cliente', 'mostrar' => 'nombres'],
            ['n' => 'id_usuario', 'l' => 'Vendedor', 't' => 'fk', 'req' => 1, 'tabla' => 'usuario', 'pk' => 'id_usuario', 'mostrar' => 'nombres'],
            ['n' => 'fecha', 'l' => 'Fecha', 't' => 'datetime', 'form' => false],
            ['n' => 'total', 'l' => 'Total (S/)', 't' => 'number', 'step' => '0.01', 'req' => 1],
        ],
    ],
    'detalle_venta' => [
        'titulo' => 'Detalle de ventas', 'pk' => 'id_detalle', 'buscar' => [],
        'campos' => [
            ['n' => 'id_venta', 'l' => 'N° venta', 't' => 'fk', 'req' => 1, 'tabla' => 'venta', 'pk' => 'id_venta', 'mostrar' => 'id_venta'],
            ['n' => 'id_producto', 'l' => 'Producto', 't' => 'fk', 'req' => 1, 'tabla' => 'producto', 'pk' => 'id_producto', 'mostrar' => 'nombre'],
            ['n' => 'cantidad', 'l' => 'Cantidad', 't' => 'number', 'step' => '1', 'req' => 1],
            ['n' => 'precio_unit', 'l' => 'Precio unit. (S/)', 't' => 'number', 'step' => '0.01', 'req' => 1],
            ['n' => 'subtotal', 'l' => 'Subtotal (S/)', 't' => 'number', 'step' => '0.01', 'req' => 1],
        ],
    ],
];
