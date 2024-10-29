<?php
// Inicia a sessão
session_start();

// Finaliza a sessão
session_destroy();

// Redireciona para a página de login
header("Location: loginAdmin.php");
exit();
?>