<?php

require_once "auth.php";

$busca = trim($_GET["busca"] ?? "");

if ($busca !== "") {

    $stmt = $pdo->prepare("
        SELECT * FROM clientes
        WHERE nome LIKE ? OR email LIKE ? OR telefone LIKE ?
        ORDER BY nome
    ");

    $termo = "%" . $busca . "%";
    $stmt->execute([$termo, $termo, $termo]);

} else {

    $stmt = $pdo->query("SELECT * FROM clientes ORDER BY nome");
}

$clientes = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Clientes</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h1>Gerenciamento de Clientes</h1>

<div class="topo">
    <a href="index.php">← Voltar</a> |
    <a href="cliente_inserir.php">➕ Cadastrar cliente</a>
</div>

<form method="GET" class="topo">
    <input type="text" name="busca" style="width:300px"
           placeholder="Digite nome, e-mail ou telefone"
           value="<?= e($busca) ?>">
    <button type="submit">Buscar</button>
</form>

<table>
<tr>
    <th>ID</th>
    <th>Nome</th>
    <th>CPF</th>
    <th>E-mail</th>
    <th>Telefone</th>
    <th>Endereço</th>
    <th>Ações</th>
</tr>

<?php foreach ($clientes as $cliente): ?>
<tr>
    <td><?= (int) $cliente["id"] ?></td>
    <td><?= e($cliente["nome"]) ?></td>
    <td><?= e(cpf_mascarar($cliente["cpf"])) ?></td>
    <td><?= e($cliente["email"]) ?></td>
    <td><?= e($cliente["telefone"]) ?></td>
    <td><?= e($cliente["endereco"]) ?></td>
    <td>
        <a href="cliente_editar.php?id=<?= (int) $cliente["id"] ?>">Editar</a>
        |
        <a href="cliente_deletar.php?id=<?= (int) $cliente["id"] ?>"
           onclick="return confirm('Deseja realmente excluir este cliente?')">Excluir</a>
    </td>
</tr>
<?php endforeach; ?>

<?php if (!$clientes): ?>
<tr><td colspan="7">Nenhum cliente encontrado.</td></tr>
<?php endif; ?>

</table>

</body>
</html>
