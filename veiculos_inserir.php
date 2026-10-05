<?php

require_once "auth.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $marca  = trim($_POST["marca"] ?? "");
    $modelo = trim($_POST["modelo"] ?? "");
    $ano    = (int) ($_POST["ano"] ?? 0);
    $placa  = strtoupper(trim($_POST["placa"] ?? ""));
    $cor    = trim($_POST["cor"] ?? "");
    $valor  = (float) ($_POST["valor_diaria"] ?? 0);

    if ($marca === "" || $modelo === "" || $placa === "" || $ano < 1900 || $valor <= 0) {

        $erro = "Preencha todos os campos obrigatórios corretamente.";

    } else {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO veiculos (marca, modelo, ano, placa, cor, valor_diaria)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([$marca, $modelo, $ano, $placa, $cor, $valor]);

            header("Location: veiculos.php");
            exit;

        } catch (PDOException $e) {

            $erro = "Não foi possível cadastrar o veículo. Verifique se a placa já existe.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo Veículo</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h1>Cadastrar Veículo</h1>

<div class="topo"><a href="veiculos.php">← Voltar</a></div>

<?php if ($erro): ?>
<p class="erro"><?= e($erro) ?></p>
<?php endif; ?>

<form method="POST" class="card">

    <label>Marca *</label>
    <input type="text" name="marca" value="<?= e($_POST["marca"] ?? "") ?>" required>
    <br><br>

    <label>Modelo *</label>
    <input type="text" name="modelo" value="<?= e($_POST["modelo"] ?? "") ?>" required>
    <br><br>

    <label>Ano *</label>
    <input type="number" name="ano" value="<?= e($_POST["ano"] ?? "") ?>" required>
    <br><br>

    <label>Placa *</label>
    <input type="text" name="placa" maxlength="10" value="<?= e($_POST["placa"] ?? "") ?>" required>
    <br><br>

    <label>Cor</label>
    <input type="text" name="cor" value="<?= e($_POST["cor"] ?? "") ?>">
    <br><br>

    <label>Valor da diária *</label>
    <input type="number" step="0.01" min="0.01" name="valor_diaria" value="<?= e($_POST["valor_diaria"] ?? "") ?>" required>
    <br><br>

    <button type="submit">Cadastrar</button>

</form>

</body>
</html>
