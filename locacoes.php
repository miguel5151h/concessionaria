<?php

require_once "auth.php";

$sql = "SELECT l.id, l.data_retirada, l.data_devolucao, l.valor_diaria,
               l.valor_total, l.status,
               c.nome AS cliente,
               v.marca, v.modelo, v.placa
        FROM locacoes l
        INNER JOIN clientes c ON c.id = l.id_cliente
        INNER JOIN veiculos v ON v.id = l.id_veiculo
        ORDER BY l.data_retirada ASC";

$locacoes = $pdo->query($sql)->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Locações</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h1>Gerenciamento de Locações</h1>

<div class="topo">
    <a href="index.php">← Voltar</a> |
    <a href="locacoes_inserir.php">➕ Nova Locação</a>
</div>

<table>
<tr>
    <th>ID</th><th>Cliente</th><th>Veículo</th><th>Placa</th><th>Retirada</th>
    <th>Devolução</th><th>Diária</th><th>Total</th><th>Status</th><th>Ações</th>
</tr>

<?php foreach ($locacoes as $l): ?>
<tr>
    <td><?= (int) $l["id"] ?></td>
    <td><?= e($l["cliente"]) ?></td>
    <td><?= e($l["marca"] . " " . $l["modelo"]) ?></td>
    <td><?= e($l["placa"]) ?></td>
    <td><?= data_br($l["data_retirada"]) ?></td>
    <td><?= data_br($l["data_devolucao"]) ?></td>
    <td><?= dinheiro($l["valor_diaria"]) ?></td>
    <td><?= dinheiro($l["valor_total"]) ?></td>
    <td><?= e($l["status"]) ?></td>
    <td>
        <a href="locacoes_editar.php?id=<?= (int) $l["id"] ?>">Editar</a>
        |
        <a href="locacoes_apagar.php?id=<?= (int) $l["id"] ?>"
           onclick="return confirm('Deseja realmente excluir esta locação?')">Excluir</a>
    </td>
</tr>
<?php endforeach; ?>

<?php if (!$locacoes): ?>
<tr><td colspan="10">Nenhuma locação cadastrada.</td></tr>
<?php endif; ?>

</table>

</body>
</html>
