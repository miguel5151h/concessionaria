<?php

require_once "auth.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: clientes.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);

$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: clientes.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome     = trim($_POST["nome"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $endereco = trim($_POST["endereco"] ?? "");

    $stmt = $pdo->prepare("
        UPDATE clientes
        SET nome = ?, email = ?, telefone = ?, endereco = ?
        WHERE id = ?
    ");

    $stmt->execute([$nome, $email, $telefone, $endereco, $id]);

    header("Location: clientes.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Cliente</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h1>Editar Cliente</h1>

<div class="topo"><a href="clientes.php">← Voltar</a></div>

<form method="POST" class="card">

    <label>Nome</label>
    <input type="text" name="nome" value="<?= e($cliente["nome"]) ?>" required>
    <br><br>

    <label>CPF</label>
    <input type="text" value="<?= e(cpf_mascarar($cliente["cpf"])) ?>" disabled>
    <br><br>

    <label>E-mail</label>
    <input type="email" name="email" value="<?= e($cliente["email"]) ?>" required>
    <br><br>

    <label>Telefone</label>
    <input type="text" name="telefone" value="<?= e($cliente["telefone"]) ?>">
    <br><br>

    <label>Endereço</label>
    <input type="text" name="endereco" value="<?= e($cliente["endereco"]) ?>">
    <br><br>

    <button type="submit">Salvar alterações</button>

</form>

</body>
</html>
