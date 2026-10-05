<?php

require_once "auth.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($id) {

    try {

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT id_veiculo, status FROM locacoes WHERE id = ?");
        $stmt->execute([$id]);
        $locacao = $stmt->fetch();

        if ($locacao) {

            $stmt = $pdo->prepare("DELETE FROM locacoes WHERE id = ?");
            $stmt->execute([$id]);

            // Só libera o veículo se a locação ainda estava aberta
            if ($locacao["status"] === "Aberta") {

                $stmt = $pdo->prepare("
                    UPDATE veiculos SET status = 'Disponível'
                    WHERE id = ? AND status = 'Alugado'
                ");
                $stmt->execute([$locacao["id_veiculo"]]);
            }
        }

        $pdo->commit();

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        die("Erro ao excluir a locação. <a href='locacoes.php'>Voltar</a>");
    }
}

header("Location: locacoes.php");
exit;
