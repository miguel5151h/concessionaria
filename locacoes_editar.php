<?php

require_once "auth.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: locacoes.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM locacoes WHERE id = ?");
$stmt->execute([$id]);

$locacao = $stmt->fetch();

if (!$locacao) {
    header("Location: locacoes.php");
    exit;
}

$erro = "";
$statusValidos = ["Aberta", "Finalizada", "Cancelada"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $retirada  = $_POST["retirada"] ?? "";
    $devolucao = $_POST["devolucao"] ?? "";
    $status    = $_POST["status"] ?? "";

    if ($retirada === "" || $devolucao === "" || !in_array($status, $statusValidos, true)) {

        $erro = "Preencha todos os campos.";

    } elseif ($devolucao < $retirada) {

        $erro = "Data de devolução inválida.";

    } else {

        try {

            $pdo->beginTransaction();

            // Trava o veículo da locação
            $stmt = $pdo->prepare("SELECT status FROM veiculos WHERE id = ? FOR UPDATE");
            $stmt->execute([$locacao["id_veiculo"]]);
            $statusVeiculo = $stmt->fetchColumn();

            // Reabrir uma locação exige veículo disponível
            if ($status === "Aberta" && $locacao["status"] !== "Aberta" && $statusVeiculo !== "Disponível") {
                throw new Exception("Não é possível reabrir: o veículo não está disponível.");
            }

            $dias = (new DateTime($retirada))->diff(new DateTime($devolucao))->days;
            if ($dias < 1) {
                $dias = 1;
            }

            $valorTotal = $dias * $locacao["valor_diaria"];

            $stmt = $pdo->prepare("
                UPDATE locacoes
                SET data_retirada = ?, data_devolucao = ?, valor_total = ?, status = ?
                WHERE id = ?
            ");

            $stmt->execute([$retirada, $devolucao, $valorTotal, $status, $id]);

            // Aberta = veículo alugado; Finalizada/Cancelada = veículo liberado
            $novoStatusVeiculo = $status === "Aberta" ? "Alugado" : "Disponível";

            if ($statusVeiculo !== "Manutenção" || $status === "Aberta") {
                $stmt = $pdo->prepare("UPDATE veiculos SET status = ? WHERE id = ?");
                $stmt->execute([$novoStatusVeiculo, $locacao["id_veiculo"]]);
            }

            $pdo->commit();

            header("Location: locacoes.php");
            exit;

        } catch (Exception $ex) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $erro = $ex->getMessage();
        }
    }

    $locacao["data_retirada"]  = $retirada;
    $locacao["data_devolucao"] = $devolucao;
    $locacao["status"]         = $status;
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Locação</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h1>Editar Locação</h1>

<div class="topo"><a href="locacoes.php">← Voltar</a></div>

<?php if ($erro): ?>
<p class="erro"><strong>Erro:</strong> <?= e($erro) ?></p>
<?php endif; ?>

<form method="POST" class="card">

    <label>Data da retirada:</label>
    <input type="date" name="retirada" value="<?= e($locacao["data_retirada"]) ?>" required>
    <br><br>

    <label>Data da devolução:</label>
    <input type="date" name="devolucao" value="<?= e($locacao["data_devolucao"]) ?>" required>
    <br><br>

    <label>Status:</label>
    <select name="status">
        <?php foreach ($statusValidos as $s): ?>
        <option value="<?= e($s) ?>" <?= $locacao["status"] === $s ? "selected" : "" ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <button type="submit">Atualizar</button>

</form>

</body>
</html>
