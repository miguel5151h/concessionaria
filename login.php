<?php

session_start();

require_once "conexao.php";

if (isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if ($email === "" || $senha === "") {

        $erro = "Preencha todos os campos.";

    } else {

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario["senha"])) {

            session_regenerate_id(true);

            $_SESSION["usuario_id"]   = $usuario["id"];
            $_SESSION["usuario_nome"] = $usuario["nome"];
            $_SESSION["login_time"]   = time();

            header("Location: index.php");
            exit;

        } else {
            $erro = "E-mail ou senha inválidos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Locadora</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; display: flex;
               justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login { background: white; padding: 30px; width: 350px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        input { width: 100%; padding: 10px; margin: 8px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #1769aa; color: white; border: 0; cursor: pointer; }
        .erro { color: red; }
    </style>
</head>
<body>
<div class="login">
    <h2>Login</h2>

    <?php if ($erro): ?>
        <p class="erro"><?= e($erro) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>E-mail</label>
        <input type="email" name="email" required>

        <label>Senha</label>
        <input type="password" name="senha" required>

        <button type="submit">Entrar</button>
    </form>
</div>
</body>
</html>
