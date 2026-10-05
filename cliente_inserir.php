<?php

require_once "auth.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome     = trim($_POST["nome"] ?? "");
    $cpf      = preg_replace("/[^0-9]/", "", $_POST["cpf"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $endereco = trim($_POST["endereco"] ?? "");

    if ($nome === "" || $cpf === "" || $email === "") {

        $mensagem = "Preencha os campos obrigatórios.";

    } elseif (strlen($cpf) !== 11) {

        $mensagem = "O CPF deve ter 11 dígitos.";

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO clientes (nome, cpf, email, telefone, endereco)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([$nome, cpf_criptografar($cpf), $email, $telefone, $endereco]);

        header("Location: clientes.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastrar Cliente</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h1>Cadastrar Cliente</h1>

<div class="topo"><a href="clientes.php">← Voltar</a></div>

<?php if ($mensagem): ?>
<p class="erro"><?= e($mensagem) ?></p>
<?php endif; ?>

<form method="POST" class="card">

    <label>Nome *</label>
    <input type="text" name="nome" value="<?= e($_POST["nome"] ?? "") ?>" required>
    <br><br>

    <label>CPF *</label>
    <input type="text" name="cpf" value="<?= e($_POST["cpf"] ?? "") ?>" required>
    <br><br>

    <label>E-mail *</label>
    <input type="email" name="email" value="<?= e($_POST["email"] ?? "") ?>" required>
    <br><br>

    <label>Telefone</label>
    <input type="text" name="telefone" value="<?= e($_POST["telefone"] ?? "") ?>">
    <br><br>

    <label>Endereço</label>
    <input type="text" name="endereco" value="<?= e($_POST["endereco"] ?? "") ?>">
    <br><br>

    <button type="submit">Cadastrar</button>

</form>

</body>
</html>
