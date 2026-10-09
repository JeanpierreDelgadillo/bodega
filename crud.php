<?php
require __DIR__ . '/inc.php';
requiere_login();
$T = require __DIR__ . '/tablas.php';
$t = $_GET['t'] ?? '';
if (!isset($T[$t])) {
    header('Location: index.php');
    exit;
}
$def = $T[$t];
$pk = $def['pk'];
$campos = $def['campos'];
$pdo = db();
$err = '';

function vacio($v): bool { return $v === null || trim((string)$v) === ''; }

// ---------- Guardar / Eliminar (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) {
        http_response_code(400);
        exit('Solicitud inválida');
    }
    $accion = $_POST['accion'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    try {
        if ($accion === 'eliminar') {
            $pdo->prepare("DELETE FROM `$t` WHERE `$pk` = ?")->execute([$id]);
            $_SESSION['flash'] = ['ok', 'Registro eliminado.'];
            header("Location: crud.php?t=$t");
            exit;
        }
        if ($accion === 'guardar') {
            $cols = [];
            $vals = [];
            foreach ($campos as $c) {
                if (($c['form'] ?? true) === false) continue;
                $n = $c['n'];
                $v = $_POST[$n] ?? '';
                $req = !empty($c['req']);
                switch ($c['t']) {
                    case 'bool':
                        $v = isset($_POST[$n]) ? 1 : 0;
                        break;
                    case 'password':
                        if (vacio($v)) {
                            if ($id) continue 2;
                            throw new RuntimeException($c['l'] . ' es obligatoria.');
                        }
                        $v = password_hash($v, PASSWORD_DEFAULT);
                        break;
                    case 'number':
                        if (vacio($v)) {
                            if ($req) throw new RuntimeException($c['l'] . ' es obligatorio.');
                            $v = null;
                        } elseif (!is_numeric($v) || $v < 0) {
                            throw new RuntimeException($c['l'] . ' debe ser un número válido.');
                        }
                        break;
                    case 'fk':
                        if (vacio($v)) {
                            if ($req) throw new RuntimeException($c['l'] . ' es obligatorio.');
                            $v = null;
                        } else {
                            $v = (int)$v;
                        }
                        break;
                    case 'select':
                        if (!isset($c['op'][$v])) throw new RuntimeException($c['l'] . ' no es válido.');
                        break;
                    default:
                        $v = trim((string)$v);
                        if ($v === '') {
                            if ($req) throw new RuntimeException($c['l'] . ' es obligatorio.');
                            $v = null;
                        }
                }
                $cols[] = $n;
                $vals[] = $v;
            }
            if ($id) {
                $set = implode(', ', array_map(fn($c) => "`$c` = ?", $cols));
                $vals[] = $id;
                $pdo->prepare("UPDATE `$t` SET $set WHERE `$pk` = ?")->execute($vals);
            } else {
                $lista = implode(', ', array_map(fn($c) => "`$c`", $cols));
                $marcas = implode(', ', array_fill(0, count($cols), '?'));
                $pdo->prepare("INSERT INTO `$t` ($lista) VALUES ($marcas)")->execute($vals);
            }
            $_SESSION['flash'] = ['ok', 'Registro guardado.'];
            header("Location: crud.php?t=$t");
            exit;
        }
    } catch (PDOException $e) { // OJO: PDOException hereda de RuntimeException, por eso va primero
        error_log($e->getMessage());
        $err = $e->getCode() === '23000'
            ? 'No se pudo completar: el dato ya existe o tiene registros relacionados.'
            : 'Error de base de datos.';
    } catch (RuntimeException $e) {
        $err = $e->getMessage();
    }
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// ---------- Formulario (nuevo / editar) ----------
$editando = isset($_GET['nuevo']) || isset($_GET['edit']) || ($err !== '' && ($_POST['accion'] ?? '') === 'guardar');
$fila = [];
$idEdit = 0;
if ($editando) {
    if ($err !== '') {
        $fila = $_POST;
        $idEdit = (int)($_POST['id'] ?? 0);
    } elseif (isset($_GET['edit'])) {
        $idEdit = (int)$_GET['edit'];
        $st = $pdo->prepare("SELECT * FROM `$t` WHERE `$pk` = ?");
        $st->execute([$idEdit]);
        $fila = $st->fetch() ?: [];
        if (!$fila) {
            header("Location: crud.php?t=$t");
            exit;
        }
    } else {
        $fila = ['activo' => 1];
    }
}

// ---------- Listado ----------
$q = trim($_GET['q'] ?? '');
$sel = ["t.`$pk`"];
foreach ($campos as $c) {
    if ($c['t'] === 'password') continue;
    $sel[] = $c['t'] === 'fk'
        ? "(SELECT x.`{$c['mostrar']}` FROM `{$c['tabla']}` x WHERE x.`{$c['pk']}` = t.`{$c['n']}`) AS `{$c['n']}`"
        : "t.`{$c['n']}`";
}
$where = '';
$params = [];
if ($q !== '') {
    $or = [];
    foreach ($def['buscar'] as $col) {
        $or[] = "t.`$col` LIKE ?";
        $params[] = "%$q%";
    }
    if (is_numeric($q)) {
        $or[] = "t.`$pk` = ?";
        $params[] = (int)$q;
    }
    if ($or) $where = 'WHERE ' . implode(' OR ', $or);
}
$st = $pdo->prepare('SELECT ' . implode(', ', $sel) . " FROM `$t` t $where ORDER BY t.`$pk` DESC LIMIT 200");
$st->execute($params);
$filas = $st->fetchAll();

cabecera($def['titulo']);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0"><?= h($def['titulo']) ?></h3>
  <?php if (!$editando): ?><a class="btn btn-success" href="crud.php?t=<?= h($t) ?>&nuevo=1">+ Nuevo</a><?php endif; ?>
</div>
<?php if ($flash): ?><div class="alert alert-success"><?= h($flash[1]) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger"><?= h($err) ?></div><?php endif; ?>

<?php if ($editando): ?>
<div class="card shadow-sm mb-4"><div class="card-body">
  <h5 class="mb-3"><?= $idEdit ? 'Editar registro' : 'Nuevo registro' ?></h5>
  <form method="post" action="crud.php?t=<?= h($t) ?>">
    <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
    <input type="hidden" name="accion" value="guardar">
    <input type="hidden" name="id" value="<?= (int)$idEdit ?>">
    <?php foreach ($campos as $c):
        if (($c['form'] ?? true) === false) continue;
        $n = $c['n'];
        $val = $fila[$n] ?? '';
        $rq = !empty($c['req']) ? 'required' : ''; ?>
      <div class="mb-3">
        <?php if ($c['t'] === 'bool'): ?>
          <div class="form-check"><input class="form-check-input" type="checkbox" name="<?= h($n) ?>" id="f_<?= h($n) ?>" <?= $val ? 'checked' : '' ?>>
          <label class="form-check-label" for="f_<?= h($n) ?>"><?= h($c['l']) ?></label></div>
        <?php else: ?>
          <label class="form-label" for="f_<?= h($n) ?>"><?= h($c['l']) ?></label>
          <?php if ($c['t'] === 'select'): ?>
            <select class="form-select" name="<?= h($n) ?>" id="f_<?= h($n) ?>" <?= $rq ?>>
              <?php foreach ($c['op'] as $k => $lbl): ?><option value="<?= h($k) ?>" <?= (string)$val === (string)$k ? 'selected' : '' ?>><?= h($lbl) ?></option><?php endforeach; ?>
            </select>
          <?php elseif ($c['t'] === 'fk'):
            $opts = $pdo->query("SELECT `{$c['pk']}` AS v, `{$c['mostrar']}` AS l FROM `{$c['tabla']}` ORDER BY `{$c['mostrar']}`")->fetchAll(); ?>
            <select class="form-select" name="<?= h($n) ?>" id="f_<?= h($n) ?>" <?= $rq ?>>
              <option value="">-- Seleccione --</option>
              <?php foreach ($opts as $o): ?><option value="<?= h($o['v']) ?>" <?= (string)$val === (string)$o['v'] ? 'selected' : '' ?>><?= h($o['l']) ?></option><?php endforeach; ?>
            </select>
          <?php elseif ($c['t'] === 'password'): ?>
            <input class="form-control" type="password" name="<?= h($n) ?>" id="f_<?= h($n) ?>" autocomplete="new-password" <?= $idEdit ? '' : $rq . ' required' ?>>
            <?php if ($idEdit): ?><div class="form-text">Déjalo vacío para mantener la contraseña actual.</div><?php endif; ?>
          <?php elseif ($c['t'] === 'number'): ?>
            <input class="form-control" type="number" min="0" step="<?= h($c['step'] ?? '1') ?>" name="<?= h($n) ?>" id="f_<?= h($n) ?>" value="<?= h($val) ?>" <?= $rq ?>>
          <?php else: ?>
            <input class="form-control" type="text" maxlength="100" name="<?= h($n) ?>" id="f_<?= h($n) ?>" value="<?= h($val) ?>" <?= $rq ?>>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <button class="btn btn-success">Guardar</button>
    <a class="btn btn-outline-secondary" href="crud.php?t=<?= h($t) ?>">Cancelar</a>
  </form>
</div></div>
<?php endif; ?>

<form class="row g-2 mb-3" method="get">
  <input type="hidden" name="t" value="<?= h($t) ?>">
  <div class="col-auto"><input class="form-control" name="q" placeholder="Buscar..." value="<?= h($q) ?>"></div>
  <div class="col-auto"><button class="btn btn-outline-success">Buscar</button>
  <?php if ($q !== ''): ?><a class="btn btn-link" href="crud.php?t=<?= h($t) ?>">Limpiar</a><?php endif; ?></div>
</form>

<div class="table-responsive"><table class="table table-striped table-hover bg-white align-middle">
  <thead class="table-success"><tr>
    <th>#</th>
    <?php foreach ($campos as $c): if ($c['t'] === 'password') continue; ?><th><?= h($c['l']) ?></th><?php endforeach; ?>
    <th style="width:150px">Acciones</th>
  </tr></thead>
  <tbody>
  <?php foreach ($filas as $f): ?>
    <tr>
      <td><?= h($f[$pk]) ?></td>
      <?php foreach ($campos as $c): if ($c['t'] === 'password') continue;
          $v = $f[$c['n']] ?? '';
          if ($c['t'] === 'bool') $v = $v ? 'Sí' : 'No';
          elseif ($c['t'] === 'select') $v = $c['op'][$v] ?? $v; ?>
        <td><?= h($v) ?></td>
      <?php endforeach; ?>
      <td class="text-nowrap">
        <a class="btn btn-sm btn-outline-primary" href="crud.php?t=<?= h($t) ?>&edit=<?= (int)$f[$pk] ?>">Editar</a>
        <form class="d-inline" method="post" action="crud.php?t=<?= h($t) ?>" onsubmit="return confirm('¿Eliminar este registro?');">
          <input type="hidden" name="csrf" value="<?= h(csrf()) ?>">
          <input type="hidden" name="accion" value="eliminar">
          <input type="hidden" name="id" value="<?= (int)$f[$pk] ?>">
          <button class="btn btn-sm btn-outline-danger">Eliminar</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$filas): ?><tr><td colspan="20" class="text-center text-muted">Sin registros.</td></tr><?php endif; ?>
  </tbody>
</table></div>
<?php pie(); ?>
