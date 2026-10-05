<?php

require_once "auth.php";

$veiculos = $pdo->query("SELECT * FROM veiculos ORDER BY marca, modelo")->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Veículos</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h1>Gerenciamento de Veículos</h1>

<div class="topo">
    <a href="index.php">← Voltar</a> |
    <a href="veiculos_inserir.php">➕ Novo Veículo</a>
</div>

<table>
<tr>
    <th>ID</th><th>Marca</th><th>Modelo</th><th>Ano</th><th>Placa</th>
    <th>Cor</th><th>Diária</th><th>Status</th><th>Ações</th>
</tr>

<?php foreach ($veiculos as $v): ?>
<tr>
    <td><?= (int) $v["id"] ?></td>
    <td><?= e($v["marca"]) ?></td>
    <td><?= e($v["modelo"]) ?></td>
    <td><?= (int) $v["ano"] ?></td>
    <td><?= e($v["placa"]) ?></td>
    <td><?= e($v["cor"]) ?></td>
    <td><?= dinheiro($v["valor_diaria"]) ?></td>
    <td><?= e($v["status"]) ?></td>
    <td>
        <a href="veiculos_editar.php?id=<?= (int) $v["id"] ?>">Editar</a>
        |
        <a href="veiculos_apagar.php?id=<?= (int) $v["id"] ?>"
           onclick="return confirm('Deseja realmente excluir este veículo?')">Excluir</a>
    </td>
</tr>
<?php endforeach; ?>

<?php if (!$veiculos): ?>
<tr><td colspan="9">Nenhum veículo cadastrado.</td></tr>
<?php endif; ?>

</table>

</body>
</html>
