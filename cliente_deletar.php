<?php

require_once "auth.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($id) {

    try {

        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->execute([$id]);

    } catch (PDOException $e) {

        die("Não é possível excluir este cliente porque existem locações relacionadas. <a href='clientes.php'>Voltar</a>");
    }
}

header("Location: clientes.php");
exit;
