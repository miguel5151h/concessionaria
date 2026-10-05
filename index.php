<?php

require_once "auth.php";

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Sistema da Locadora</title>
<style>
body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f4; }
header { background: #1769aa; color: white; padding: 20px; }
.container { padding: 30px; }
.menu { display: flex; gap: 15px; flex-wrap: wrap; }
.menu a { background: white; padding: 25px; text-decoration: none; color: #222;
          border-radius: 10px; box-shadow: 0 0 5px #ccc; text-align: center; min-width: 110px; }
.sair { color: white; float: right; }
</style>
</head>
<body>

<header>
    <strong>Sistema da Locadora</strong>
    <a class="sair" href="logout.php">Sair</a>
</header>

<div class="container">

    <h2>Bem-vindo, <?= e($_SESSION["usuario_nome"]) ?>!</h2>

    <p>Selecione uma funcionalidade:</p>

    <div class="menu">
        <a href="clientes.php">👤<br><strong>Clientes</strong></a>
        <a href="veiculos.php">🚗<br><strong>Veículos</strong></a>
        <a href="locacoes.php">📋<br><strong>Locações</strong></a>
    </div>

</div>

</body>
</html>
