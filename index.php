<?php
// ERROR: esta línea no debería estar aquí
$variableError = "esto rompe el proyecto";
$file = 'tareas.json';
$tareas = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    if ($accion === 'crear' && !empty($_POST['tarea'])) {
        $tareas[] = ['id' => time(), 'texto' => htmlspecialchars($_POST['tarea']), 'hecha' => false, 'fecha' => date('d/m/Y H:i')];
    } elseif ($accion === 'borrar' && isset($_POST['id'])) {
        $tareas = array_filter($tareas, fn($t) => $t['id'] != $_POST['id']);
        $tareas = array_values($tareas);
    } elseif ($accion === 'completar' && isset($_POST['id'])) {
        foreach ($tareas as &$t) {
            if ($t['id'] == $_POST['id']) $t['hecha'] = !$t['hecha'];
        }
    }
    file_put_contents($file, json_encode($tareas));
    header('Location: index.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor de Tareas</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #333; }
        input[type=text] { padding: 8px; width: 70%; }
        button { padding: 8px 16px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px; }
        li { margin: 8px 0; }
    </style>
</head>
<body>
<h1>Gestor de Tareas - CRUD</h1>
<p>Total: <?= count($tareas) ?> | Pendientes: <?= count(array_filter($tareas, fn($t) => !$t['hecha'])) ?></p>
<form method="POST">
    <input type="hidden" name="accion" value="crear">
    <input type="text" name="tarea" placeholder="Nueva tarea..." required>
    <button type="submit">Añadir</button>
</form>
<ul>
<?php foreach ($tareas as $t): ?>
    <li style="text-decoration:<?= $t['hecha'] ? 'line-through' : 'none' ?>">
        <?= $t['texto'] ?> <small style="color:gray">(<?= $t['fecha'] ?? '' ?>)</small>
        <form method="POST" style="display:inline">
            <input type="hidden" name="accion" value="completar">
            <input type="hidden" name="id" value="<?= $t['id'] ?>">
            <button><?= $t['hecha'] ? 'Deshacer' : 'Completar' ?></button>
        </form>
        <form method="POST" style="display:inline">
            <input type="hidden" name="accion" value="borrar">
            <input type="hidden" name="id" value="<?= $t['id'] ?>">
            <button style="background:#dc3545">Borrar</button>
        </form>
    </li>
<?php endforeach; ?>
</ul>
</body>
</html>