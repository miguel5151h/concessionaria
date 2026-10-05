<?php

require_once "auth.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($id) {

    try {

        $stmt = $pdo->prepare("DELETE FROM veiculos WHERE id = ?");
        $stmt->execute([$id]);

    } catch (PDOException $e) {

        die("Não é possível excluir este veículo porque ele possui locações. <a href='veiculos.php'>Voltar</a>");
    }
}

header("Location: veiculos.php");
exit;
