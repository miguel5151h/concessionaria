<?php

require_once "auth.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: veiculos.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM veiculos WHERE id = ?");
$stmt->execute([$id]);

$veiculo = $stmt->fetch();

if (!$veiculo) {
    header("Location: veiculos.php");
    exit;
}

$erro = "";
$statusValidos = ["Disponível", "Alugado", "Manutenção"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $marca  = trim($_POST["marca"] ?? "");
    $modelo = trim($_POST["modelo"] ?? "");
    $ano    = (int) ($_POST["ano"] ?? 0);
    $placa  = strtoupper(trim($_POST["placa"] ?? ""));
    $cor    = trim($_POST["cor"] ?? "");
    $valor  = (float) ($_POST["valor_diaria"] ?? 0);
    $status = $_POST["status"] ?? "";

    if ($marca === "" || $modelo === "" || $placa === "" || $ano < 1900 || $valor <= 0
        || !in_array($status, $statusValidos, true)) {

        $erro = "Preencha todos os campos corretamente.";

    } else {

        try {

            $stmt = $pdo->prepare("
                UPDATE veiculos
                SET marca = ?, modelo = ?, ano = ?, placa = ?,
                    cor = ?, valor_diaria = ?, status = ?
                WHERE id = ?
            ");

            $stmt->execute([$marca, $modelo, $ano, $placa, $cor, $valor, $status, $id]);

            header("Location: veiculos.php");
            exit;

        } catch (PDOException $e) {

            $erro = "Não foi possível atualizar. Verifique se a placa já existe.";
        }
    }

    // Mantém no formulário o que foi digitado
    $veiculo = array_merge($veiculo, [
        "marca" => $marca, "modelo" => $modelo, "ano" => $ano, "placa" => $placa,
        "cor" => $cor, "valor_diaria" => $valor, "status" => $status,
    ]);
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Veículo</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h1>Editar Veículo</h1>

<div class="topo"><a href="veiculos.php">← Voltar</a></div>

<?php if ($erro): ?>
<p class="erro"><?= e($erro) ?></p>
<?php endif; ?>

<form method="POST" class="card">

    <label>Marca</label>
    <input type="text" name="marca" value="<?= e($veiculo["marca"]) ?>" required>
    <br><br>

    <label>Modelo</label>
    <input type="text" name="modelo" value="<?= e($veiculo["modelo"]) ?>" required>
    <br><br>

    <label>Ano</label>
    <input type="number" name="ano" value="<?= (int) $veiculo["ano"] ?>" required>
    <br><br>

    <label>Placa</label>
    <input type="text" name="placa" maxlength="10" value="<?= e($veiculo["placa"]) ?>" required>
    <br><br>

    <label>Cor</label>
    <input type="text" name="cor" value="<?= e($veiculo["cor"]) ?>">
    <br><br>

    <label>Valor da diária</label>
    <input type="number" step="0.01" min="0.01" name="valor_diaria" value="<?= e($veiculo["valor_diaria"]) ?>" required>
    <br><br>

    <label>Status</label>
    <select name="status">
        <?php foreach ($statusValidos as $s): ?>
        <option value="<?= e($s) ?>" <?= $veiculo["status"] === $s ? "selected" : "" ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <button type="submit">Atualizar</button>

</form>

</body>
</html>
