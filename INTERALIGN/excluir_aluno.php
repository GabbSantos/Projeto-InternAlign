<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_email']) || !isset($_SESSION['usuario_nome'])) {
    // Redireciona para a página de login se não estiver logado
    header("Location: login.php");
    exit();
}

// Verifica se o ID do aluno foi passado via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Se não foi passado o ID, redireciona de volta para a página principal
    header("Location: dashboard.php");
    exit();
}

// Inclua o arquivo de conexão com o banco de dados
require_once 'database/connect.php';

// Obtém o ID do aluno da URL
$id = $_GET['id'];

// Consulta SQL para excluir o aluno pelo ID
$stmt = $pdo->prepare("DELETE FROM alunos WHERE id = ?");
$stmt->execute([$id]);

// Redireciona de volta para a página principal após a exclusão
header("Location: dashboard.php");
exit();
?>