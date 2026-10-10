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
    
    <div class="d-flex justify-content-center mb-4 mt-2">
      <div style="width: 140px; height: 140px;">
        <img src="img/logo.png" style="width: 100%; height: 100%; object-fit: contain;">
      </div>
    </div>

    <h4 class="mb-4 text-center fw-bold" style="color: #333; letter-spacing: -0.3px;">Portal administrativo</h4>
    
    <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
      <div class="mb-3">
        <label class="form-label fw-medium text-secondary">Usuario</label>
        <input class="form-control" name="usuario" required autofocus style="border-radius: 6px;">
      </div>
      
      
      <div class="mb-3">
        <label class="form-label fw-medium text-secondary">Contraseña</label>
        <div class="input-group">
          <input class="form-control" type="password" name="clave" id="campo_clave" required style="border-top-left-radius: 6px; border-bottom-left-radius: 6px;">
          <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center" type="button" id="btn_ojo" style="border-top-right-radius: 6px; border-bottom-right-radius: 6px; width: 45px;">
            
            <svg id="svg_ojo" xmlns="http://w3.org" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
              <path id="path_ojo" d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c3.75 0 5.7 4.291 5.7 4.5s-1.95 4.5-5.7 4.5a6 6 0 0 1-1.897-.301l.712.712zM6.164 5.164A3.5 3.5 0 0 0 1 8c0 .193 1.95 4.5 5.7 4.5a6 6 0 0 0 2.457-.512l.766.767A7 7 0 0 1 8 13.5c-5 0-8-5.5-8-5.5s.939-1.721 2.641-3.238l.79.791zm6.417 6.417l-10-10 .708-.708 10 10zM11.963 9.84l-.853-.853a2.5 2.5 0 0 0-3.111-3.111l-.853-.853a3.5 3.5 0 0 1 4.817 4.816z"/>
            </svg>
          </button>
        </div>
      </div>
      
      <button class="btn text-white w-100 fw-bold py-2 shadow-sm" style="background: linear-gradient(135deg, #1d9059 0%, #0a4729 100%); border: none; border-radius: 6px;">Ingresar</button>
    </form>
  </div></div>
</div></div>


<script>
  document.getElementById('btn_ojo').addEventListener('click', function () {
    const campo = document.getElementById('campo_clave');
    const path = document.getElementById('path_ojo');
    
    
    const ojoAbierto = "M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0";
    const ojoCerrado = "M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c3.75 0 5.7 4.291 5.7 4.5s-1.95 4.5-5.7 4.5a6 6 0 0 1-1.897-.301l.712.712zM6.164 5.164A3.5 3.5 0 0 0 1 8c0 .193 1.95 4.5 5.7 4.5a6 6 0 0 0 2.457-.512l.766.767A7 7 0 0 1 8 13.5c-5 0-8-5.5-8-5.5s.939-1.721 2.641-3.238l.79.791zm6.417 6.417l-10-10 .708-.708 10 10zM11.963 9.84l-.853-.853a2.5 2.5 0 0 0-3.111-3.111l-.853-.853a3.5 3.5 0 0 1 4.817 4.816z";

    if (campo.type === 'password') {
      campo.type = 'text';
      path.setAttribute('d', ojoAbierto); 
    } else {
      campo.type = 'password';
      path.setAttribute('d', ojoCerrado); 
    }
  });
</script>

<?php pie(); ?>
