<?php

require __DIR__ . '/inc.php';

requiere_login();

$T = require __DIR__ . '/tablas.php';

$estilos = [
    'usuario' => [
        'imagen' => 'img/usuarios.png',
        'accion' => 'Ver usuarios'
    ],
    'categoria' => [
        'imagen' => 'img/categorias.png',
        'accion' => 'Ver categorías'
    ],
    'producto' => [
        'imagen' => 'img/productos.png',
        'accion' => 'Ver productos'
    ],
    'cliente' => [
        'imagen' => 'img/clientes.png',
        'accion' => 'Ver clientes'
    ],
    'venta' => [
        'imagen' => 'img/ventas.png',
        'accion' => 'Ver ventas'
    ],
    'detalle_venta' => [
        'imagen' => 'img/detalle_ventas.png',
        'accion' => 'Ver detalles'
    ]
];

cabecera('Inicio');

?>

<div class="panel-encabezado">

    <h2 class="panel-titulo">
        Panel de administración
    </h2>

    <p class="panel-subtitulo">
        Gestión general de la Bodega Don Pepe
    </p>

</div>

<div class="row g-3">

<?php foreach ($T as $k => $d): ?>

    <?php

    $n = db()
        ->query("SELECT COUNT(*) FROM `" . $k . "`")
        ->fetchColumn();

    $conf = $estilos[$k] ?? [
        'imagen' => 'img/modulo.png',
        'accion' => 'Gestionar'
    ];

    ?>

    <div class="col-sm-6 col-lg-4">
        <div class="card tarjeta-modulo h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="cabecera-tarjeta">
                        <div class="icono-modulo">
                            <img
                                src="<?= h($conf['imagen']) ?>"
                                alt="<?= h($d['titulo']) ?>"
                            >
                        </div>
                        <h5 class="titulo-modulo">
                            <?= h($d['titulo']) ?>
                        </h5>
                    </div>
                    <div class="numero-registros">
                        <?= (int) $n ?>
                    </div>
                    <div class="texto-registros">
                        <?= (int) $n === 1
                            ? 'Registro almacenado'
                            : 'Registros almacenados'
                        ?>
                    </div>
                </div>

                <div class="mt-2 text-center">
                    <a
                        href="crud.php?t=<?= urlencode($k) ?>"
                        class="btn boton-modulo"
                    >
                        <?= h($conf['accion']) ?>
                        <span class="flecha">
                            →
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php endforeach; ?>

</div>

<?php pie(); ?>