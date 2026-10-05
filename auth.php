<?php

// Incluir no topo de toda página protegida.

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Sessão expira após 30 minutos sem atividade
if (time() - ($_SESSION["login_time"] ?? 0) > 1800) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$_SESSION["login_time"] = time();

require_once "conexao.php";
