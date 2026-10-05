<?php

// ==========================================
// CONFIGURAÇÃO DO BANCO
// XAMPP usa a porta 3306 por padrão; ajuste se o seu MySQL usa outra (ex.: 3307).
// ==========================================
$host    = "localhost";
$porta   = 3307;
$banco   = "concessionaria";
$usuario = "root";
$senha   = "";

// Chave usada para criptografar o CPF dos clientes
const CHAVE_CPF = "chave-secreta-locadora-2026";

try {

    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

} catch (PDOException $erro) {

    die("Erro na conexão com o banco: " . $erro->getMessage());

}

// ==========================================
// FUNÇÕES AUXILIARES
// ==========================================

function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}

function cpf_chave()
{
    return hash("sha256", CHAVE_CPF, true);
}

function cpf_criptografar($cpf)
{
    $iv = random_bytes(16);

    $cifrado = openssl_encrypt($cpf, "AES-256-CBC", cpf_chave(), OPENSSL_RAW_DATA, $iv);

    return base64_encode($iv . $cifrado);
}

function cpf_descriptografar($valor)
{
    $dados = base64_decode((string) $valor, true);

    if ($dados === false || strlen($dados) < 32) {
        return "";
    }

    $cpf = openssl_decrypt(
        substr($dados, 16),
        "AES-256-CBC",
        cpf_chave(),
        OPENSSL_RAW_DATA,
        substr($dados, 0, 16)
    );

    return $cpf === false ? "" : $cpf;
}

function cpf_mascarar($valor)
{
    $cpf = cpf_descriptografar($valor);

    return strlen($cpf) === 11 ? "***.***.***-" . substr($cpf, -2) : "—";
}

function data_br($data)
{
    return date("d/m/Y", strtotime($data));
}

function dinheiro($valor)
{
    return "R$ " . number_format((float) $valor, 2, ",", ".");
}
