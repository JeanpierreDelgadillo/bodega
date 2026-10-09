<?php
require __DIR__ . '/inc.php';
requiere_login();
$T = require __DIR__ . '/tablas.php';
cabecera('Inicio');
?>
<h3 class="mb-4">Panel de mantenimiento</h3>
<div class="row g-3">
<?php foreach ($T as $k => $d):
    $n = db()->query('SELECT COUNT(*) FROM `' . $k . '`')->fetchColumn(); ?>
  <div class="col-sm-6 col-lg-4"><div class="card shadow-sm h-100"><div class="card-body">
    <h5><?= h($d['titulo']) ?></h5>
    <p class="text-muted mb-3"><?= (int)$n ?> registros</p>
    <a class="btn btn-outline-success btn-sm" href="crud.php?t=<?= h($k) ?>">Administrar</a>
  </div></div></div>
<?php endforeach; ?>
</div>
<?php pie(); ?>
