<?php

require_once "auth.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $cliente   = filter_input(INPUT_POST, "cliente", FILTER_VALIDATE_INT);
    $veiculo   = filter_input(INPUT_POST, "veiculo", FILTER_VALIDATE_INT);
    $retirada  = $_POST["retirada"] ?? "";
    $devolucao = $_POST["devolucao"] ?? "";

    if (!$cliente || !$veiculo) {

        $erro = "Selecione o cliente e o veículo.";

    } elseif ($retirada === "" || $devolucao === "") {

        $erro = "Informe as datas de retirada e devolução.";

    } elseif ($devolucao < $retirada) {

        $erro = "A data de devolução não pode ser anterior à retirada.";

    } else {

        try {

            $pdo->beginTransaction();

            // FOR UPDATE trava a linha do veículo durante o cadastro
            $stmt = $pdo->prepare("SELECT * FROM veiculos WHERE id = ? FOR UPDATE");
            $stmt->execute([$veiculo]);
            $dadosVeiculo = $stmt->fetch();

            if (!$dadosVeiculo) {
                throw new Exception("Veículo não encontrado.");
            }

            if ($dadosVeiculo["status"] !== "Disponível") {
                throw new Exception("O veículo não está disponível.");
            }

            $stmt = $pdo->prepare("SELECT id FROM clientes WHERE id = ?");
            $stmt->execute([$cliente]);

            if (!$stmt->fetch()) {
                throw new Exception("Cliente não encontrado.");
            }

            $valorDiaria = (float) $dadosVeiculo["valor_diaria"];

            // Mesmo dia = 1 diária
            $dias = (new DateTime($retirada))->diff(new DateTime($devolucao))->days;
            if ($dias < 1) {
                $dias = 1;
            }

            $valorTotal = $dias * $valorDiaria;

            $stmt = $pdo->prepare("
                INSERT INTO locacoes
                (id_cliente, id_veiculo, data_retirada, data_devolucao, valor_diaria, valor_total, status)
                VALUES (?, ?, ?, ?, ?, ?, 'Aberta')
            ");

            $stmt->execute([$cliente, $veiculo, $retirada, $devolucao, $valorDiaria, $valorTotal]);

            $stmt = $pdo->prepare("UPDATE veiculos SET status = 'Alugado' WHERE id = ?");
            $stmt->execute([$veiculo]);

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
}

$clientes = $pdo->query("SELECT id, nome, cpf FROM clientes ORDER BY nome")->fetchAll();

$veiculos = $pdo->query("
    SELECT id, marca, modelo, placa, valor_diaria
    FROM veiculos
    WHERE status = 'Disponível'
    ORDER BY marca, modelo
")->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Nova Locação</title>
<link rel="stylesheet" href="estilo.css">
</head>
<body>

<h1>Cadastrar Locação</h1>

<div class="topo"><a href="locacoes.php">← Voltar</a></div>

<?php if ($erro): ?>
<p class="erro"><strong>Erro:</strong> <?= e($erro) ?></p>
<?php endif; ?>

<form method="POST" class="card">

    <label>Cliente:</label>
    <select name="cliente" required>
        <option value="">Selecione um cliente</option>
        <?php foreach ($clientes as $c): ?>
        <option value="<?= (int) $c["id"] ?>">
            <?= e($c["nome"] . " - " . cpf_mascarar($c["cpf"])) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Veículo:</label>
    <select name="veiculo" required>
        <option value="">Selecione um veículo</option>
        <?php foreach ($veiculos as $v): ?>
        <option value="<?= (int) $v["id"] ?>">
            <?= e($v["marca"] . " " . $v["modelo"] . " - " . $v["placa"] . " (" . dinheiro($v["valor_diaria"]) . "/dia)") ?>
        </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Data da retirada:</label>
    <input type="date" name="retirada" required>
    <br><br>

    <label>Data da devolução:</label>
    <input type="date" name="devolucao" required>
    <br><br>

    <button type="submit">Cadastrar Locação</button>

</form>

</body>
</html>
