<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluindo o arquivo de conexão com o banco de dados
require_once '../database/connect.php';

// Verifica se o formulário de registro foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Sanitiza e valida os dados do formulário
    $nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $curso = filter_var($_POST['curso'], FILTER_SANITIZE_STRING);
    $cpf = filter_var($_POST['cpf'], FILTER_SANITIZE_STRING);
    $telefone = filter_var($_POST['telefone'], FILTER_SANITIZE_STRING);
    $situacao = filter_var($_POST['situacao'], FILTER_SANITIZE_STRING);
    $empresa = filter_var($_POST['empresa'], FILTER_SANITIZE_STRING);

    // Verifica se o e-mail é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("E-mail inválido");
    }

    // Verifica se os campos obrigatórios estão preenchidos
    if (empty($nome) || empty($email) || empty($cpf)) {
        die("Todos os campos obrigatórios devem ser preenchidos.");
    }

    // Tenta inserir os dados no banco de dados
    try {
        $stmt = $pdo->prepare("INSERT INTO alunos (nome, email, curso, cpf, telefone, situacao, empresa) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $curso, $cpf, $telefone, $situacao, $empresa]);
        
        // Redireciona para alguma página de sucesso
        header("Location: ../dashboard.php?result=success");
        exit();
    } catch (PDOException $e) {
        echo "Erro ao inserir os dados: " . $e->getMessage();
    }
}
