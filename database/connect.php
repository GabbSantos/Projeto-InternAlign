<?php
// Configurações do banco de dados
$dbHost = 'localhost'; // endereço do banco de dados
$dbName = 'upload'; // nome do banco de dados
$dbUser = 'root'; // nome de usuário do banco de dados
$dbPass = ''; // senha do banco de dados

try {
    // Conexão com o banco de dados utilizando PDO
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);

    // Configura o PDO para lançar exceções em caso de erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Configura o PDO para retornar os resultados como arrays associativos
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Se ocorrer um erro na conexão, exibe uma mensagem de erro
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>