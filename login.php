<?php
require __DIR__ . '/inc.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        $error = 'Solicitud inválida. Recarga la página.';
    } else {
        $st = db()->prepare("SELECT id_usuario, nombres, clave FROM usuario WHERE usuario = ? AND rol = 'admin' AND activo = 1");
        $st->execute([trim($_POST['usuario'] ?? '')]);
        $u = $st->fetch();
        if ($u && password_verify($_POST['clave'] ?? '', $u['clave'])) {
            session_regenerate_id(true);
            $_SESSION['uid'] = $u['id_usuario'];
            $_SESSION['nombre'] = $u['nombres'];
            header('Location: index.php');
            exit;
        }
        $error = 'Usuario o contraseña incorrectos.';
    }
}
cabecera('Ingresar', false);
?>
<div class="row justify-content-center"><div class="col-md-4">
  <div class="card shadow-sm"><div class="card-body">
    <h4 class="mb-3 text-center">Portal administrativo</h4>
    <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
      <div class="mb-3"><label class="form-label">Usuario</label><input class="form-control" name="usuario" required autofocus></div>
      <div class="mb-3"><label class="form-label">Contraseña</label><input class="form-control" type="password" name="clave" required></div>
      <button class="btn btn-success w-100">Ingresar</button>
    </form>
  </div></div>
</div></div>
<?php pie(); ?>
